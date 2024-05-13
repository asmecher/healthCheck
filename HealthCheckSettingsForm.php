<?php

/**
 * @file HealthCheckSettingsForm.php
 *
 * Copyright (c) 2017-2023 Simon Fraser University
 * Copyright (c) 2017-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class HealthCheckSettingsForm
 * @brief Settings form class for the HealthCheck plugin.
 */

namespace APP\plugins\generic\healthCheck;

use APP\core\Application;
use APP\notification\Notification;
use APP\notification\NotificationManager;
use APP\template\TemplateManager;
use PKP\db\DAORegistry;
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;
use PKP\plugins\PluginRegistry;

class HealthCheckSettingsForm extends Form {

    public HealthCheckPlugin $plugin;

    /**
     * Defines the settings form's template and adds
     * validation checks.
     *
     * Always add POST and CSRF validation to secure
     * your form.
     */
    public function __construct(HealthCheckPlugin $plugin)
    {
        parent::__construct($plugin->getTemplateResource('settings.tpl'));

        $this->plugin = $plugin;

        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * Load settings already saved in the database
     *
     * Settings are stored by context, so that each journal, press,
     * or preprint server can have different settings.
     */
    public function initData()
    {
        $context = Application::get()
            ->getRequest()
            ->getContext();

        $contextId = $context
            ? $context->getId()
            : Application::CONTEXT_SITE;

        $this->setData(
            'publicationStatement',
            $this->plugin->getSetting(
                $contextId,
                'publicationStatement'
            )
        );

        parent::initData();
    }

    /**
     * Load data that was submitted with the form
     */
    public function readInputData()
    {
        $this->readUserVars(['publicationStatement']);

        parent::readInputData();
    }

    /**
     * Fetch any additional data needed for your form.
     *
     * Data assigned to the form using $this->setData() during the
     * initData() or readInputData() methods will be passed to the
     * template.
     *
     * In the example below, the plugin name is passed to the
     * template so that it can be used in the URL that the form is
     * submitted to.
     */
    public function fetch($request, $template = null, $display = false)
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->plugin->getName());

        // Fetch all installed plugins.
        $plugins = PluginRegistry::getAllPlugins();

        // Use version DAO to retrieve version.
        $versionDao = DAORegistry::getDAO('VersionDAO');

        // Begin assembling markup for settings page.
        $list = [];
        foreach ($plugins as $plugin) {

            // Extract version for given plugin from version DAO.
            $pluginInfo = explode('/', $plugin->getPluginPath());
            $productType = $pluginInfo[0] . '.' . $pluginInfo[1];
            $productName = $pluginInfo[2];
            $currentVersion = $versionDao->getCurrentVersion($productType, $productName);

            // Assemble list item.
            $list[] =  $plugin->getName() . ' (' . $currentVersion->getVersionString() . ')';
        }

        // Assign to template variable.
        $templateMgr->assign('pluginList', $list);
        return parent::fetch($request, $template, $display);
    }

    /**
     * Save the plugin settings and notify the user
     * that the save was successful
     */
    public function execute(...$functionArgs)
    {
        $context = Application::get()
            ->getRequest()
            ->getContext();

        $contextId = $context
            ? $context->getId()
            : Application::CONTEXT_SITE;

        $this->plugin->updateSetting(
            $contextId,
            'publicationStatement',
            $this->getData('publicationStatement')
        );

        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification(
            Application::get()->getRequest()->getUser()->getId(),
            Notification::NOTIFICATION_TYPE_SUCCESS,
            ['contents' => __('common.changesSaved')]
        );

        return parent::execute();
    }
}
