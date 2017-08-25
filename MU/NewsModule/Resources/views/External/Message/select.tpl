{* Purpose of this template: Display a popup selector for Forms and Content integration *}
{assign var='baseID' value='message'}
<div id="itemSelectorInfo" class="hidden" data-base-id="{$baseID}" data-selected-id="{$selectedId|default:0}"></div>
<div class="row">
    <div class="col-sm-8">

        {if $properties ne null && is_array($properties)}
            {gt text='All' assign='lblDefault'}
            {nocache}
            {foreach key='propertyName' item='propertyId' from=$properties}
                <div class="form-group">
                    {assign var='hasMultiSelection' value=$categoryHelper->hasMultipleSelection('message', $propertyName)}
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
                    <label for="{$baseID}_{$categorySelectorId}{$propertyName}" class="col-sm-3 control-label">{$categoryLabel}:</label>
                    <div class="col-sm-9">
                        {selector_category name="`$baseID`_`$categorySelectorName``$propertyName`" field='id' selectedValue=$catIds.$propertyName|default:null categoryRegistryModule='MUNewsModule' categoryRegistryTable="`$objectType`Entity" categoryRegistryProperty=$propertyName defaultText=$lblDefault editLink=false multipleSize=$categorySelectorSize cssClass='form-control'}
                    </div>
                </div>
            {/foreach}
            {/nocache}
        {/if}
        <div class="form-group">
            <label for="{$baseID}Id" class="col-sm-3 control-label">{gt text='Message'}:</label>
            <div class="col-sm-9">
                <select id="{$baseID}Id" name="id" class="form-control">
                    {foreach item='message' from=$items}
                        <option value="{$message->getKey()}"{if $selectedId eq $message->getKey()} selected="selected"{/if}>{$message->getTitle()}</option>
                    {foreachelse}
                        <option value="0">{gt text='No entries found.'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$baseID}Sort" class="col-sm-3 control-label">{gt text='Sort by'}:</label>
            <div class="col-sm-9">
                <select id="{$baseID}Sort" name="sort" class="form-control">
                    <option value="workflowState"{if $sort eq 'workflowState'} selected="selected"{/if}>{gt text='Workflow state'}</option>
                    <option value="title"{if $sort eq 'title'} selected="selected"{/if}>{gt text='Title'}</option>
                    <option value="startText"{if $sort eq 'startText'} selected="selected"{/if}>{gt text='Start text'}</option>
                    <option value="imageUpload1"{if $sort eq 'imageUpload1'} selected="selected"{/if}>{gt text='Image upload 1'}</option>
                    <option value="mainText"{if $sort eq 'mainText'} selected="selected"{/if}>{gt text='Main text'}</option>
                    <option value="author"{if $sort eq 'author'} selected="selected"{/if}>{gt text='Author'}</option>
                    <option value="notes"{if $sort eq 'notes'} selected="selected"{/if}>{gt text='Notes'}</option>
                    <option value="displayOnIndex"{if $sort eq 'displayOnIndex'} selected="selected"{/if}>{gt text='Display on index'}</option>
                    <option value="messageLanguage"{if $sort eq 'messageLanguage'} selected="selected"{/if}>{gt text='Message language'}</option>
                    <option value="allowComments"{if $sort eq 'allowComments'} selected="selected"{/if}>{gt text='Allow comments'}</option>
                    <option value="imageUpload2"{if $sort eq 'imageUpload2'} selected="selected"{/if}>{gt text='Image upload 2'}</option>
                    <option value="imageUpload3"{if $sort eq 'imageUpload3'} selected="selected"{/if}>{gt text='Image upload 3'}</option>
                    <option value="imageUpload4"{if $sort eq 'imageUpload4'} selected="selected"{/if}>{gt text='Image upload 4'}</option>
                    <option value="noEndDate"{if $sort eq 'noEndDate'} selected="selected"{/if}>{gt text='No end date'}</option>
                    <option value="createdDate"{if $sort eq 'createdDate'} selected="selected"{/if}>{gt text='Creation date'}</option>
                    <option value="createdBy"{if $sort eq 'createdBy'} selected="selected"{/if}>{gt text='Creator'}</option>
                    <option value="updatedDate"{if $sort eq 'updatedDate'} selected="selected"{/if}>{gt text='Update date'}</option>
                    <option value="updatedBy"{if $sort eq 'updatedBy'} selected="selected"{/if}>{gt text='Updater'}</option>
                </select>
                <select id="{$baseID}SortDir" name="sortdir" class="form-control">
                    <option value="asc"{if $sortdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
                    <option value="desc"{if $sortdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$baseID}SearchTerm" class="col-sm-3 control-label">{gt text='Search for'}:</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <input type="text" id="{$baseID}SearchTerm" name="q" class="form-control" />
                    <span class="input-group-btn">
                        <input type="button" id="mUNewsModuleSearchGo" name="gosearch" value="{gt text='Filter'}" class="btn btn-default" />
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div id="{$baseID}Preview" style="border: 1px dotted #a3a3a3; padding: .2em .5em">
            <p><strong>{gt text='Message information'}</strong></p>
            {img id='ajaxIndicator' modname='core' set='ajax' src='indicator_circle.gif' alt='' class='hidden'}
            <div id="{$baseID}PreviewContainer">&nbsp;</div>
        </div>
    </div>
</div>
