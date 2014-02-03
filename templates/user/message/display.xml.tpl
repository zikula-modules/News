{* purpose of this template: messages display xml view in user area *}
{munewsTemplateHeaders contentType='text/xml'}<?xml version="1.0" encoding="{charset}" ?>
{getbaseurl assign='baseurl'}
{include file='user/message/include.xml' item=$message}
