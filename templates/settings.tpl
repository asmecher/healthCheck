{**
 * templates/settings.tpl
 *
 * Copyright (c) 2014-2023 Simon Fraser University
 * Copyright (c) 2003-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Settings form for the health check plugin.
 *}
<script>
	$(function() {ldelim}
		$('#healthCheckSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form
	class="pkp_form"
	id="healthCheckSettings"
	method="POST"
	action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}"
>
	<!-- Always add the csrf token to secure your form -->
	{csrf}

    <h3>{translate key="plugins.generic.healthCheck.activePlugins"}</h3>

    <div id="server-environment-details">
        <p><b>PHP Version:</b> {$phpVersion}</p>
        <p><b>PHP Error Log:</b> {$phpErrorLog}</p>
    </div>

    <div id="plugin-list-section">
        <ul id="plugin-list">
            {foreach from=$pluginList item=plugin}
                <li>{$plugin.name|escape} ({$plugin.version|escape})</li>
            {/foreach}
        </ul>
    </div>

    <h3>{translate key="plugins.generic.healthCheck.ojsConfig"}</h3>
        <ul id="plugin-list">
	    <li>
		{if $isSaltConfigured}
		    {translate key="plugins.generic.healthCheck.ojsConfig.saltConfigured"}
		{else}
		    {translate key="plugins.generic.healthCheck.ojsConfig.saltNotConfigured"}
		{/if}
	</ul>


	{fbvFormButtons submitText="common.save"}
</form>
