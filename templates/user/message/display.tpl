{* purpose of this template: messages display view in user area *}
{include file='user/header.tpl'}
<div class="munews-message munews-display">
    {gt text='Message' assign='templateTitle'}
    {assign var='templateTitle' value=$message->getTitleFromDisplayPattern()|default:$templateTitle}
    {pagesetvar name='title' value=$templateTitle|@html_entity_decode}
    <h2>{$templateTitle|notifyfilters:'munews.filter_hooks.messages.filter'} <small>({$message.workflowState|munewsObjectState:false|lower})</small>{icon id='itemActionsTrigger' type='options' size='extrasmall' __alt='Actions' class='z-pointer z-hide'}</h2>

    <dl>
        <dt>{gt text='State'}</dt>
        <dd>{$message.workflowState|munewsGetListEntry:'message':'workflowState'|safetext}</dd>
        <dt>{gt text='Title'}</dt>
        <dd>{$message.title}</dd>
        <dt>{gt text='Start text'}</dt>
        <dd>{$message.startText}</dd>
        <dt>{gt text='Image upload1'}</dt>
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
        <dt>{gt text='Main text'}</dt>
        <dd>{$message.mainText}</dd>
        <dt>{gt text='Image upload2'}</dt>
        <dd>{if $message.imageUpload2 ne ''}
          <a href="{$message.imageUpload2FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload2Meta.isImage} rel="imageviewer[message]"{/if}>
          {if $message.imageUpload2Meta.isImage}
              {thumb image=$message.imageUpload2FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload2 tag=true img_alt=$message->getTitleFromDisplayPattern()}
          {else}
              {gt text='Download'} ({$message.imageUpload2Meta.size|munewsGetFileSize:$message.imageUpload2FullPath:false:false})
          {/if}
          </a>
        {else}&nbsp;{/if}
        </dd>
        <dt>{gt text='Image upload3'}</dt>
        <dd>{if $message.imageUpload3 ne ''}
          <a href="{$message.imageUpload3FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload3Meta.isImage} rel="imageviewer[message]"{/if}>
          {if $message.imageUpload3Meta.isImage}
              {thumb image=$message.imageUpload3FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload3 tag=true img_alt=$message->getTitleFromDisplayPattern()}
          {else}
              {gt text='Download'} ({$message.imageUpload3Meta.size|munewsGetFileSize:$message.imageUpload3FullPath:false:false})
          {/if}
          </a>
        {else}&nbsp;{/if}
        </dd>
        <dt>{gt text='Image upload4'}</dt>
        <dd>{if $message.imageUpload4 ne ''}
          <a href="{$message.imageUpload4FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload4Meta.isImage} rel="imageviewer[message]"{/if}>
          {if $message.imageUpload4Meta.isImage}
              {thumb image=$message.imageUpload4FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload4 tag=true img_alt=$message->getTitleFromDisplayPattern()}
          {else}
              {gt text='Download'} ({$message.imageUpload4Meta.size|munewsGetFileSize:$message.imageUpload4FullPath:false:false})
          {/if}
          </a>
        {else}&nbsp;{/if}
        </dd>
        <dt>{gt text='Muimage album'}</dt>
        <dd>{$message.muimageAlbum}</dd>
        <dt>{gt text='Weight'}</dt>
        <dd>{$message.weight}</dd>
        <dt>{gt text='Start date'}</dt>
        <dd>{$message.startDate|dateformat:'datetimebrief'}</dd>
        <dt>{gt text='No end date'}</dt>
        <dd>{$message.noEndDate|yesno:true}</dd>
        <dt>{gt text='End date'}</dt>
        <dd>{$message.endDate|dateformat:'datetimebrief'}</dd>
        <dt>{gt text='Options'}</dt>
        <dd>{$message.options}</dd>
        <dt>{gt text='Relation to'}</dt>
        <dd>{$message.relationTo}</dd>
        
    </dl>
    {include file='user/include_categories_display.tpl' obj=$message}
    {include file='user/include_standardfields_display.tpl' obj=$message}

    {if !isset($smarty.get.theme) || $smarty.get.theme ne 'Printer'}
        {* include display hooks *}
        {notifydisplayhooks eventname='munews.ui_hooks.messages.display_view' id=$message.id urlobject=$currentUrlObject assign='hooks'}
        {foreach key='providerArea' item='hook' from=$hooks}
            {$hook}
        {/foreach}
        {if count($message._actions) gt 0}
            <p id="itemActions">
            {foreach item='option' from=$message._actions}
                <a href="{$option.url.type|munewsActionUrl:$option.url.func:$option.url.arguments}" title="{$option.linkTitle|safetext}" class="z-icon-es-{$option.icon}">{$option.linkText|safetext}</a>
            {/foreach}
            </p>
            <script type="text/javascript">
            /* <![CDATA[ */
                document.observe('dom:loaded', function() {
                    munewsInitItemActions('message', 'display', 'itemActions');
                });
            /* ]]> */
            </script>
        {/if}
    {/if}
</div>
{include file='user/footer.tpl'}
