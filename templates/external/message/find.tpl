{* Purpose of this template: Display a popup selector of messages for scribite integration *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{lang}" lang="{lang}">
<head>
    <title>{gt text='Search and select message'}</title>
    <link type="text/css" rel="stylesheet" href="{$baseurl}style/core.css" />
    <link type="text/css" rel="stylesheet" href="{$baseurl}modules/MUNews/style/style.css" />
    <link type="text/css" rel="stylesheet" href="{$baseurl}modules/MUNews/style/finder.css" />
    {assign var='ourEntry' value=$modvars.ZConfig.entrypoint}
    <script type="text/javascript">/* <![CDATA[ */
        if (typeof(Zikula) == 'undefined') {var Zikula = {};}
        Zikula.Config = {'entrypoint': '{{$ourEntry|default:'index.php'}}', 'baseURL': '{{$baseurl}}'}; /* ]]> */</script>
        <script type="text/javascript" src="{$baseurl}javascript/ajax/proto_scriptaculous.combined.min.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/helpers/Zikula.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/livepipe/livepipe.combined.min.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/helpers/Zikula.UI.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/helpers/Zikula.ImageViewer.js"></script>
    <script type="text/javascript" src="{$baseurl}modules/MUNews/javascript/MUNews_finder.js"></script>
{if $editorName eq 'tinymce'}
    <script type="text/javascript" src="{$baseurl}modules/Scribite/includes/tinymce/tiny_mce_popup.js"></script>
{/if}
</head>
<body>
    <form action="{$ourEntry|default:'index.php'}" id="mUNewsSelectorForm" method="get" class="z-form">
    <div>
        <input type="hidden" name="module" value="MUNews" />
        <input type="hidden" name="type" value="external" />
        <input type="hidden" name="func" value="finder" />
        <input type="hidden" name="objectType" value="{$objectType}" />
        <input type="hidden" name="editor" id="editorName" value="{$editorName}" />

        <fieldset>
            <legend>{gt text='Search and select message'}</legend>
            
            {if $properties ne null && is_array($properties)}
                {gt text='All' assign='lblDefault'}
                {nocache}
                {foreach key='propertyName' item='propertyId' from=$properties}
                    <div class="z-formrow categoryselector">
                        {modapifunc modname='MUNews' type='category' func='hasMultipleSelection' ot=$objectType registry=$propertyName assign='hasMultiSelection'}
                        {gt text='Category' assign='categoryLabel'}
                        {assign var='categorySelectorId' value='catid'}
                        {assign var='categorySelectorName' value='catid'}
                        {assign var='categorySelectorSize' value='1'}
                        {if $hasMultiSelection eq true}
                            {gt text='Categories' assign='categoryLabel'}
                            {assign var='categorySelectorName' value='catids'}
                            {assign var='categorySelectorId' value='catids__'}
                            {assign var='categorySelectorSize' value='8'}
                        {/if}
                        <label for="{$categorySelectorId}{$propertyName}">{$categoryLabel}</label>
                        &nbsp;
                            {selector_category name="`$categorySelectorName``$propertyName`" field='id' selectedValue=$catIds.$propertyName categoryRegistryModule='MUNews' categoryRegistryTable=$objectType categoryRegistryProperty=$propertyName defaultText=$lblDefault editLink=false multipleSize=$categorySelectorSize}
                            <span class="z-sub z-formnote">{gt text='This is an optional filter.'}</span>
                    </div>
                {/foreach}
                {/nocache}
            {/if}

            <div class="z-formrow">
                <label for="mUNewsPasteAs">{gt text='Paste as'}:</label>
                    <select id="mUNewsPasteAs" name="pasteas">
                        <option value="1">{gt text='Link to the message'}</option>
                        <option value="2">{gt text='ID of message'}</option>
                    </select>
            </div>
            <br />

            <div class="z-formrow">
                <label for="mUNewsObjectId">{gt text='Message'}:</label>
                    <div id="munewsItemContainer">
                        <ul>
                        {foreach item='message' from=$items}
                            <li>
                                <a href="#" onclick="munews.finder.selectItem({$message.id})" onkeypress="munews.finder.selectItem({$message.id})">{$message->getTitleFromDisplayPattern()}</a>
                                <input type="hidden" id="url{$message.id}" value="{modurl modname='MUNews' type='user' func='display' ot='message' id=$message.id slug=$message.slug fqurl=true}" />
                                <input type="hidden" id="title{$message.id}" value="{$message->getTitleFromDisplayPattern()|replace:"\"":""}" />
                                <input type="hidden" id="desc{$message.id}" value="{capture assign='description'}{if $message.startText ne ''}{$message.startText}{/if}
                                {/capture}{$description|strip_tags|replace:"\"":""}" />
                            </li>
                        {foreachelse}
                            <li>{gt text='No entries found.'}</li>
                        {/foreach}
                        </ul>
                    </div>
            </div>

            <div class="z-formrow">
                <label for="mUNewsSort">{gt text='Sort by'}:</label>
                    <select id="mUNewsSort" name="sort" style="width: 150px" class="z-floatleft" style="margin-right: 10px">
                    <option value="id"{if $sort eq 'id'} selected="selected"{/if}>{gt text='Id'}</option>
                    <option value="workflowState"{if $sort eq 'workflowState'} selected="selected"{/if}>{gt text='Workflow state'}</option>
                    <option value="title"{if $sort eq 'title'} selected="selected"{/if}>{gt text='Title'}</option>
                    <option value="startText"{if $sort eq 'startText'} selected="selected"{/if}>{gt text='Start text'}</option>
                    <option value="imageUpload1"{if $sort eq 'imageUpload1'} selected="selected"{/if}>{gt text='Image upload1'}</option>
                    <option value="mainText"{if $sort eq 'mainText'} selected="selected"{/if}>{gt text='Main text'}</option>
                    <option value="imageUpload2"{if $sort eq 'imageUpload2'} selected="selected"{/if}>{gt text='Image upload2'}</option>
                    <option value="imageUpload3"{if $sort eq 'imageUpload3'} selected="selected"{/if}>{gt text='Image upload3'}</option>
                    <option value="imageUpload4"{if $sort eq 'imageUpload4'} selected="selected"{/if}>{gt text='Image upload4'}</option>
                    <option value="muimageAlbum"{if $sort eq 'muimageAlbum'} selected="selected"{/if}>{gt text='Muimage album'}</option>
                    <option value="weight"{if $sort eq 'weight'} selected="selected"{/if}>{gt text='Weight'}</option>
                    <option value="startDate"{if $sort eq 'startDate'} selected="selected"{/if}>{gt text='Start date'}</option>
                    <option value="noEndDate"{if $sort eq 'noEndDate'} selected="selected"{/if}>{gt text='No end date'}</option>
                    <option value="endDate"{if $sort eq 'endDate'} selected="selected"{/if}>{gt text='End date'}</option>
                    <option value="options"{if $sort eq 'options'} selected="selected"{/if}>{gt text='Options'}</option>
                    <option value="relationTo"{if $sort eq 'relationTo'} selected="selected"{/if}>{gt text='Relation to'}</option>
                    <option value="createdDate"{if $sort eq 'createdDate'} selected="selected"{/if}>{gt text='Creation date'}</option>
                    <option value="createdUserId"{if $sort eq 'createdUserId'} selected="selected"{/if}>{gt text='Creator'}</option>
                    <option value="updatedDate"{if $sort eq 'updatedDate'} selected="selected"{/if}>{gt text='Update date'}</option>
                    </select>
                    <select id="mUNewsSortDir" name="sortdir" style="width: 100px">
                        <option value="asc"{if $sortdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
                        <option value="desc"{if $sortdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
                    </select>
            </div>

            <div class="z-formrow">
                <label for="mUNewsPageSize">{gt text='Page size'}:</label>
                    <select id="mUNewsPageSize" name="num" style="width: 50px; text-align: right">
                        <option value="5"{if $pager.itemsperpage eq 5} selected="selected"{/if}>5</option>
                        <option value="10"{if $pager.itemsperpage eq 10} selected="selected"{/if}>10</option>
                        <option value="15"{if $pager.itemsperpage eq 15} selected="selected"{/if}>15</option>
                        <option value="20"{if $pager.itemsperpage eq 20} selected="selected"{/if}>20</option>
                        <option value="30"{if $pager.itemsperpage eq 30} selected="selected"{/if}>30</option>
                        <option value="50"{if $pager.itemsperpage eq 50} selected="selected"{/if}>50</option>
                        <option value="100"{if $pager.itemsperpage eq 100} selected="selected"{/if}>100</option>
                    </select>
            </div>

            <div class="z-formrow">
                <label for="mUNewsSearchTerm">{gt text='Search for'}:</label>
                    <input type="text" id="mUNewsSearchTerm" name="searchterm" style="width: 150px" class="z-floatleft" style="margin-right: 10px" />
                    <input type="button" id="mUNewsSearchGo" name="gosearch" value="{gt text='Filter'}" style="width: 80px" />
            </div>
            
            <div style="margin-left: 6em">
                {pager display='page' rowcount=$pager.numitems limit=$pager.itemsperpage posvar='pos' template='pagercss.tpl' maxpages='10'}
            </div>
            <input type="submit" id="mUNewsSubmit" name="submitButton" value="{gt text='Change selection'}" />
            <input type="button" id="mUNewsCancel" name="cancelButton" value="{gt text='Cancel'}" />
            <br />
        </fieldset>
    </div>
    </form>

    <script type="text/javascript">
    /* <![CDATA[ */
        document.observe('dom:loaded', function() {
            munews.finder.onLoad();
        });
    /* ]]> */
    </script>

    {*
    <div class="munews-finderform">
        <fieldset>
            {modfunc modname='MUNews' type='admin' func='edit'}
        </fieldset>
    </div>
    *}
</body>
</html>
