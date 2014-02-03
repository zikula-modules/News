{* Purpose of this template: Display item information for previewing from other modules *}
<dl id="message{$message.id}">
<dt>{$message->getTitleFromDisplayPattern()|notifyfilters:'munews.filter_hooks.messages.filter'|htmlentities}</dt>
<dd>{if $message.imageUpload1 ne ''}
  <a href="{$message.imageUpload1FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload1Meta.isImage} rel="imageviewer[message]"{/if}>
  {if $message.imageUpload1Meta.isImage}
      {thumb image=$message.imageUpload1FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload1 tag=true img_alt=$message->getTitleFromDisplayPattern()}
  {else}
      {gt text='Download'} ({$message.imageUpload1Meta.size|munewsGetFileSize:$message.imageUpload1FullPath:false:false})
  {/if}
  </a>
{else}&nbsp;{/if}
</dd>
{if $message.startText ne ''}<dd>{$message.startText}</dd>{/if}
<dd>{assignedcategorieslist categories=$message.categories doctrine2=true}</dd>
</dl>
