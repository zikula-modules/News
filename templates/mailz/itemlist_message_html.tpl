{* Purpose of this template: Display messages in html mailings *}
{*
<ul>
{foreach item='message' from=$items}
    <li>
        <a href="{modurl modname='MUNews' type='user' func='display' ot=$objectType id=$message.id slug=$message.slug fqurl=true}">{$message->getTitleFromDisplayPattern()}
        </a>
    </li>
{foreachelse}
    <li>{gt text='No messages found.'}</li>
{/foreach}
</ul>
*}

{include file='contenttype/itemlist_message_display_description.tpl'}
