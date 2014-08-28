{* purpose of this template: messages view view in user area *}
{include file='user/header.tpl'}
<div class="munews-message munews-view">
    {gt text='Message list' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <h2>{$templateTitle}</h2>

    {if $canBeCreated}
        {checkpermissionblock component='MUNews:Message:' instance='::' level='ACCESS_ADD'}
            {gt text='Create message' assign='createTitle'}
            <a href="{modurl modname='MUNews' type='user' func='edit' ot='message'}" title="{$createTitle}" class="z-icon-es-add">{$createTitle}</a>
        {/checkpermissionblock}
    {/if}
    {assign var='own' value=0}
    {if isset($showOwnEntries) && $showOwnEntries eq 1}
        {assign var='own' value=1}
    {/if}
    {assign var='all' value=0}
    {if isset($showAllEntries) && $showAllEntries eq 1}
        {gt text='Back to paginated view' assign='linkTitle'}
        <a href="{modurl modname='MUNews' type='user' func='view' ot='message'}" title="{$linkTitle}" class="z-icon-es-view">
            {$linkTitle}
        </a>
        {assign var='all' value=1}
    {else}
        {gt text='Show all entries' assign='linkTitle'}
        <a href="{modurl modname='MUNews' type='user' func='view' ot='message' all=1}" title="{$linkTitle}" class="z-icon-es-view">{$linkTitle}</a>
    {/if}

   {* {include file='user/message/view_quickNav.tpl' all=$all own=$own}{* see template file for available options *}

   {* <table class="z-datatable">
        <colgroup>
            <col id="cWorkflowState" />
            <col id="cTitle" />
            <col id="cStartText" />
            <col id="cImageUpload1" />
            <col id="cMainText" />
            <col id="cImageUpload2" />
            <col id="cImageUpload3" />
            <col id="cImageUpload4" />
            <col id="cMuimageAlbum" />
            <col id="cWeight" />
            <col id="cStartDate" />
            <col id="cNoEndDate" />
            <col id="cEndDate" />
            <col id="cItemActions" />
        </colgroup>
        <thead>
        <tr>
            {assign var='catIdListMainString' value=','|implode:$catIdList.Main}
            <th id="hWorkflowState" scope="col" class="z-left">
                {sortlink __linktext='State' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='workflowState' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hTitle" scope="col" class="z-left">
                {sortlink __linktext='Title' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='title' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hStartText" scope="col" class="z-left">
                {sortlink __linktext='Start text' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='startText' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hImageUpload1" scope="col" class="z-left">
                {sortlink __linktext='Image upload1' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='imageUpload1' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hMainText" scope="col" class="z-left">
                {sortlink __linktext='Main text' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='mainText' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hImageUpload2" scope="col" class="z-left">
                {sortlink __linktext='Image upload2' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='imageUpload2' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hImageUpload3" scope="col" class="z-left">
                {sortlink __linktext='Image upload3' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='imageUpload3' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hImageUpload4" scope="col" class="z-left">
                {sortlink __linktext='Image upload4' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='imageUpload4' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hMuimageAlbum" scope="col" class="z-right">
                {sortlink __linktext='Muimage album' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='muimageAlbum' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hWeight" scope="col" class="z-right">
                {sortlink __linktext='Weight' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='weight' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hStartDate" scope="col" class="z-left">
                {sortlink __linktext='Start date' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='startDate' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hNoEndDate" scope="col" class="z-center">
                {sortlink __linktext='No end date' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='noEndDate' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hEndDate" scope="col" class="z-left">
                {sortlink __linktext='End date' currentsort=$sort modname='MUNews' type='user' func='view' ot='message' sort='endDate' sortdir=$sdir all=$all own=$own catidMain=$catIdListMainString workflowState=$workflowState searchterm=$searchterm pageSize=$pageSize noEndDate=$noEndDate}
            </th>
            <th id="hItemActions" scope="col" class="z-right z-order-unsorted">{gt text='Actions'}</th>
        </tr>
        </thead>
        <tbody>
    
       {foreach item='message' from=$items}
        <tr class="{cycle values='z-odd, z-even'}">
            <td headers="hWorkflowState" class="z-left z-nowrap">
                {$message.workflowState|munewsObjectState}
            </td>
            <td headers="hTitle" class="z-left">
                <a href="{modurl modname='MUNews' type='user' func='display' ot='message' id=$message.id slug=$message.slug}" title="{gt text='View detail page'}">{$message.title|notifyfilters:'munews.filterhook.messages'}</a>
            </td>
            <td headers="hStartText" class="z-left">
                {$message.startText}
            </td>
            <td headers="hImageUpload1" class="z-left">
                {if $message.imageUpload1 ne ''}
                  <a href="{$message.imageUpload1FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload1Meta.isImage} rel="imageviewer[message]"{/if}>
                  {if $message.imageUpload1Meta.isImage}
                      {thumb image=$message.imageUpload1FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload1 tag=true img_alt=$message->getTitleFromDisplayPattern()}
                  {else}
                      {gt text='Download'} ({$message.imageUpload1Meta.size|munewsGetFileSize:$message.imageUpload1FullPath:false:false})
                  {/if}
                  </a>
                {else}&nbsp;{/if}
            </td>
            <td headers="hMainText" class="z-left">
                {$message.mainText}
            </td>
            <td headers="hImageUpload2" class="z-left">
                {if $message.imageUpload2 ne ''}
                  <a href="{$message.imageUpload2FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload2Meta.isImage} rel="imageviewer[message]"{/if}>
                  {if $message.imageUpload2Meta.isImage}
                      {thumb image=$message.imageUpload2FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload2 tag=true img_alt=$message->getTitleFromDisplayPattern()}
                  {else}
                      {gt text='Download'} ({$message.imageUpload2Meta.size|munewsGetFileSize:$message.imageUpload2FullPath:false:false})
                  {/if}
                  </a>
                {else}&nbsp;{/if}
            </td>
            <td headers="hImageUpload3" class="z-left">
                {if $message.imageUpload3 ne ''}
                  <a href="{$message.imageUpload3FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload3Meta.isImage} rel="imageviewer[message]"{/if}>
                  {if $message.imageUpload3Meta.isImage}
                      {thumb image=$message.imageUpload3FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload3 tag=true img_alt=$message->getTitleFromDisplayPattern()}
                  {else}
                      {gt text='Download'} ({$message.imageUpload3Meta.size|munewsGetFileSize:$message.imageUpload3FullPath:false:false})
                  {/if}
                  </a>
                {else}&nbsp;{/if}
            </td>
            <td headers="hImageUpload4" class="z-left">
                {if $message.imageUpload4 ne ''}
                  <a href="{$message.imageUpload4FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload4Meta.isImage} rel="imageviewer[message]"{/if}>
                  {if $message.imageUpload4Meta.isImage}
                      {thumb image=$message.imageUpload4FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload4 tag=true img_alt=$message->getTitleFromDisplayPattern()}
                  {else}
                      {gt text='Download'} ({$message.imageUpload4Meta.size|munewsGetFileSize:$message.imageUpload4FullPath:false:false})
                  {/if}
                  </a>
                {else}&nbsp;{/if}
            </td>
            <td headers="hMuimageAlbum" class="z-right">
                {$message.muimageAlbum}
            </td>
            <td headers="hWeight" class="z-right">
                {$message.weight}
            </td>
            <td headers="hStartDate" class="z-left">
                {$message.startDate|dateformat:'datetimebrief'}
            </td>
            <td headers="hNoEndDate" class="z-center">
                {$message.noEndDate|yesno:true}
            </td>
            <td headers="hEndDate" class="z-left">
                {$message.endDate|dateformat:'datetimebrief'}
            </td>
            <td id="itemActions{$message.id}" headers="hItemActions" class="z-right z-nowrap z-w02">
                {if count($message._actions) gt 0}
                    {foreach item='option' from=$message._actions}
                        <a href="{$option.url.type|munewsActionUrl:$option.url.func:$option.url.arguments}" title="{$option.linkTitle|safetext}"{if $option.icon eq 'preview'} target="_blank"{/if}>{icon type=$option.icon size='extrasmall' alt=$option.linkText|safetext}</a>
                    {/foreach}
                    {icon id="itemActions`$message.id`Trigger" type='options' size='extrasmall' __alt='Actions' class='z-pointer z-hide'}
                    <script type="text/javascript">
                    /* <![CDATA[ */
                        document.observe('dom:loaded', function() {
                            munewsInitItemActions('message', 'view', 'itemActions{{$message.id}}');
                        });
                    /* ]]> */
                    </script>
                {/if}
            </td>
        </tr>
    {foreachelse}
        <tr class="z-datatableempty">
          <td class="z-left" colspan="14">
        {gt text='No messages found.'}
          </td>
        </tr>
    {/foreach} *}
    
    {foreach item='message' from=$items}
        <div class="munews-view">
            <div class="munews-view-title">
                <h2> {if $showAuthor eq 1}{useravatar uid=$message.createdUserId size=30}{/if}<a href="{modurl modname='MUNews' type='user' func='display' ot='message' id=$message.id slug=$message.slug}" title="{gt text='View detail page'}">{$message.title|notifyfilters:'munews.filterhook.messages'}</a></h2>   
            </div>
            <div class="munews-view-start">
                {if $message.imageUpload1 ne ''}
                  <a href="{$message.imageUpload1FullPathURL}" title="{$message->getTitleFromDisplayPattern()|replace:"\"":""}"{if $message.imageUpload1Meta.isImage} rel="imageviewer[message]"{/if}>
                  {if $message.imageUpload1Meta.isImage}
                      {thumb image=$message.imageUpload1FullPath objectid="message-`$message.id`" preset=$messageThumbPresetImageUpload1 tag=true img_alt=$message->getTitleFromDisplayPattern()}
                  {else}
                      {gt text='Download'} ({$message.imageUpload1Meta.size|munewsGetFileSize:$message.imageUpload1FullPath:false:false})
                  {/if}
                  </a>
                {else}&nbsp;{/if}  
                {$message.startText|safehtml|truncate:400} 
                {if $message.mainText ne ''}<br />
                    <a href="{modurl modname='MUNews' type='user' func='display' ot='message' id=$message.id}">{gt text='Read more'}</a>
                {/if}
            </div>
        </div>
    
    {foreachelse}
        <tr class="z-datatableempty">
          <td class="z-left" colspan="14">
        {gt text='No messages found.'}
          </td>
        </tr>
    {/foreach}
    
        </tbody>
    </table>
    
    {if !isset($showAllEntries) || $showAllEntries ne 1}
        {pager rowcount=$pager.numitems limit=$pager.itemsperpage display='page' modname='MUNews' type='user' func='view' ot='message'}
    {/if}

    
   {* {notifydisplayhooks eventname='munews.ui_hooks.messages.display_view' urlobject=$currentUrlObject assign='hooks'}
    {foreach key='providerArea' item='hook' from=$hooks}
        {$hook}
    {/foreach} *}
</div>
{include file='user/footer.tpl'}
