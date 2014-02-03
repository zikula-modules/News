{* Purpose of this template: Display messages within an external context *}
{foreach item='message' from=$items}
    <h3>{$message->getTitleFromDisplayPattern()}</h3>
    <p><a href="{modurl modname='MUNews' type='user' func='display' ot=$objectType id=$message.id slug=$message.slug}">{gt text='Read more'}</a>
    </p>
{/foreach}
