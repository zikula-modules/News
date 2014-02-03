{* purpose of this template: messages view xml view in admin area *}
{munewsTemplateHeaders contentType='text/xml'}<?xml version="1.0" encoding="{charset}" ?>
<messages>
{foreach item='item' from=$items}
    {include file='admin/message/include.xml'}
{foreachelse}
    <noMessage />
{/foreach}
</messages>
