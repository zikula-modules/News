{* Purpose of this template: show moderation block *}
{if count($moderationObjects) gt 0}
    <ul>
    {foreach item='modItem' from=$moderationObjects}
        <li><a href="{modurl modname='MUNews' type='admin' func='view' ot=$modItem.objectType workflowState=$modItem.state}" class="z-bold">{$modItem.message}</a></li>
    {/foreach}
    </ul>
{/if}
