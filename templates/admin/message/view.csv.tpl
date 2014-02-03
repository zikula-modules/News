{* purpose of this template: messages view csv view in admin area *}
{munewsTemplateHeaders contentType='text/comma-separated-values; charset=iso-8859-15' asAttachment=true filename='Messages.csv'}
{strip}"{gt text='Title'}";"{gt text='Start text'}";"{gt text='Image upload1'}";"{gt text='Main text'}";"{gt text='Image upload2'}";"{gt text='Image upload3'}";"{gt text='Image upload4'}";"{gt text='Muimage album'}";"{gt text='Weight'}";"{gt text='Start date'}";"{gt text='No end date'}";"{gt text='End date'}";"{gt text='Options'}";"{gt text='Relation to'}";"{gt text='Workflow state'}"
{/strip}
{foreach item='message' from=$items}
{strip}
    "{$message.title}";"{$message.startText}";"{$message.imageUpload1}";"{$message.mainText}";"{$message.imageUpload2}";"{$message.imageUpload3}";"{$message.imageUpload4}";"{$message.muimageAlbum}";"{$message.weight}";"{$message.startDate|dateformat:'datetimebrief'}";"{if !$message.noEndDate}0{else}1{/if}";"{$message.endDate|dateformat:'datetimebrief'}";"{$message.options}";"{$message.relationTo}";"{$item.workflowState|munewsObjectState:false|lower}"
{/strip}
{/foreach}
