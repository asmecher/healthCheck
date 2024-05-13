<p>Hello world</p>
<p>OJS version {$ojs_version|escape}</p>

<ul>
    {foreach from=$plugins item=plugin}
        <li>{$plugin|escape}</li>
    {/foreach}
</ul>
