{* Purpose of this template: Display messages within an external context *}
<dl>
    {foreach item='message' from=$items}
        <dt>{$message->getTitleFromDisplayPattern()}</dt>
        {if $message.startText}
            <dd>{$message.startText|truncate:200:"..."}</dd>
        {/if}
        <dd><a href="{modurl modname='MUNews' type='user' func='display' ot=$objectType id=$message.id slug=$message.slug}">{gt text='Read more'}</a>
        </dd>
    {foreachelse}
        <dt>{gt text='No entries found.'}</dt>
    {/foreach}
</dl>
