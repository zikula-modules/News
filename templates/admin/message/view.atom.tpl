{* purpose of this template: messages atom feed in admin area *}
{munewsTemplateHeaders contentType='application/atom+xml'}<?xml version="1.0" encoding="{charset assign='charset'}{if $charset eq 'ISO-8859-15'}ISO-8859-1{else}{$charset}{/if}" ?>
<feed xmlns="http://www.w3.org/2005/Atom">
{gt text='Latest messages' assign='channelTitle'}
{gt text='A direct feed showing the list of messages' assign='channelDesc'}
    <title type="text">{$channelTitle}</title>
    <subtitle type="text">{$channelDesc} - {$modvars.ZConfig.slogan}</subtitle>
    <author>
        <name>{$modvars.ZConfig.sitename}</name>
    </author>
{assign var='numItems' value=$items|@count}
{if $numItems}
{capture assign='uniqueID'}tag:{$baseurl|replace:'http://':''|replace:'/':''},{$items[0].createdDate|dateformat|default:$smarty.now|dateformat:'%Y-%m-%d'}:{modurl modname='MUNews' type='admin' func='display' ot='message' id=$items[0].id slug=$items[0].slug}{/capture}
    <id>{$uniqueID}</id>
    <updated>{$items[0].updatedDate|default:$smarty.now|dateformat:'%Y-%m-%dT%H:%M:%SZ'}</updated>
{/if}
    <link rel="alternate" type="text/html" hreflang="{lang}" href="{modurl modname='MUNews' type='admin' func='main' fqurl=1}" />
    <link rel="self" type="application/atom+xml" href="{php}echo substr(\System::getBaseURL(), 0, strlen(\System::getBaseURL())-1);{/php}{getcurrenturi}" />
    <rights>Copyright (c) {php}echo date('Y');{/php}, {$baseurl}</rights>

{foreach item='message' from=$items}
    <entry>
        <title type="html">{$message->getTitleFromDisplayPattern()|notifyfilters:'munews.filterhook.messages'}</title>
        <link rel="alternate" type="text/html" href="{modurl modname='MUNews' type='admin' func='display' ot='message' id=$message.id slug=$message.slug fqurl='1'}" />

        {capture assign='uniqueID'}tag:{$baseurl|replace:'http://':''|replace:'/':''},{$message.createdDate|dateformat|default:$smarty.now|dateformat:'%Y-%m-%d'}:{modurl modname='MUNews' type='admin' func='display' ot='message' id=$message.id slug=$message.slug}{/capture}
        <id>{$uniqueID}</id>
        {if isset($message.updatedDate) && $message.updatedDate ne null}
            <updated>{$message.updatedDate|dateformat:'%Y-%m-%dT%H:%M:%SZ'}</updated>
        {/if}
        {if isset($message.createdDate) && $message.createdDate ne null}
            <published>{$message.createdDate|dateformat:'%Y-%m-%dT%H:%M:%SZ'}</published>
        {/if}
        {if isset($message.createdUserId)}
            {usergetvar name='uname' uid=$message.createdUserId assign='cr_uname'}
            {usergetvar name='name' uid=$message.createdUserId assign='cr_name'}
            <author>
               <name>{$cr_name|default:$cr_uname}</name>
               <uri>{usergetvar name='_UYOURHOMEPAGE' uid=$message.createdUserId assign='homepage'}{$homepage|default:'-'}</uri>
               <email>{usergetvar name='email' uid=$message.createdUserId}</email>
            </author>
        {/if}

        <summary type="html">
            <![CDATA[
            {$message.startText|truncate:150:"&hellip;"|default:'-'}
            ]]>
        </summary>
        <content type="html">
            <![CDATA[
            {$message.mainText|replace:'<br>':'<br />'}
            ]]>
        </content>
    </entry>
{/foreach}
</feed>
