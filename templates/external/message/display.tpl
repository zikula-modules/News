{* Purpose of this template: Display one certain message within an external context *}
{pageaddvar name='javascript' value='zikula.imageviewer'}
<div id="message{$message.id}" class="munews-external-message">
{if $displayMode eq 'link'}
    <p class="munews-external-link">
    <a href="{modurl modname='MUNews' type='user' func='display' ot='message' id=$message.id slug=$message.slug}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}">
    {$message->getTitleFromDisplayPattern()|notifyfilters:'munews.filter_hooks.messages.filter'}
    </a>
    </p>
{/if}
{checkpermissionblock component='MUNews::' instance='::' level='ACCESS_EDIT'}
    {if $displayMode eq 'embed'}
        <p class="munews-external-title">
            <strong>{$message->getTitleFromDisplayPattern()|notifyfilters:'munews.filter_hooks.messages.filter'}</strong>
        </p>
    {/if}
{/checkpermissionblock}

{if $displayMode eq 'link'}
{elseif $displayMode eq 'embed'}
    <div class="munews-external-snippet">
        {if $message.imageUpload1 ne ''}
          <a href="{$message.imageUpload1FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload1Meta.isImage} rel="imageviewer[message]"{/if}>
          {if $message.imageUpload1Meta.isImage}
              {thumb image=$message.imageUpload1FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload1 tag=true img_alt=$message->getTitleFromDisplayPattern()}
          {else}
              {gt text='Download'} ({$message.imageUpload1Meta.size|munewsGetFileSize:$message.imageUpload1FullPath:false:false})
          {/if}
          </a>
        {else}&nbsp;{/if}
        {$message.startText|safehtml}<br />
        <a href="{modurl modname='MUNews' type='user' func='display' ot='message' id=$message.id}">{gt text='Read more'}</a>
    </div>

    {* you can distinguish the context like this: *}
    {*if $source eq 'contentType'}
        ...
    {elseif $source eq 'scribite'}
        ...
    {/if*}

    {* you can enable more details about the item: *}
    {*
        <p class="munews-external-description">
            {if $message.startText ne ''}{$message.startText}<br />{/if}
            {assignedcategorieslist categories=$message.categories doctrine2=true}
        </p>
    *}
{/if}
</div>
