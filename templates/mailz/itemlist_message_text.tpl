{* Purpose of this template: Display messages in text mailings *}
{foreach item='message' from=$items}
{$message->getTitleFromDisplayPattern()}
{modurl modname='MUNews' type='user' func='display' ot=$objectType id=$message.id slug=$message.slug fqurl=true}
-----
{foreachelse}
{gt text='No messages found.'}
{/foreach}
