{* Purpose of this template: Display a popup selector for Forms and Content integration *}
{assign var='baseID' value='message'}
<div id="{$baseID}Preview" style="float: right; width: 300px; border: 1px dotted #a3a3a3; padding: .2em .5em; margin-right: 1em">
    <p><strong>{gt text='Message information'}</strong></p>
    {img id='ajax_indicator' modname='core' set='ajax' src='indicator_circle.gif' alt='' class='z-hide'}
    <div id="{$baseID}PreviewContainer">&nbsp;</div>
</div>
<br />
<br />
{assign var='leftSide' value=' style="float: left; width: 10em"'}
{assign var='rightSide' value=' style="float: left"'}
{assign var='break' value=' style="clear: left"'}

{if $properties ne null && is_array($properties)}
    {gt text='All' assign='lblDefault'}
    {nocache}
    {foreach key='propertyName' item='propertyId' from=$properties}
        <p>
            {modapifunc modname='MUNews' type='category' func='hasMultipleSelection' ot='message' registry=$propertyName assign='hasMultiSelection'}
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
            <label for="{$baseID}_{$categorySelectorId}{$propertyName}"{$leftSide}>{$categoryLabel}:</label>
            &nbsp;
            {selector_category name="`$baseID`_`$categorySelectorName``$propertyName`" field='id' selectedValue=$catIds.$propertyName categoryRegistryModule='MUNews' categoryRegistryTable=$objectType categoryRegistryProperty=$propertyName defaultText=$lblDefault editLink=false multipleSize=$categorySelectorSize}
            <br{$break} />
        </p>
    {/foreach}
    {/nocache}
{/if}
<p>
    <label for="{$baseID}Id"{$leftSide}>{gt text='Message'}:</label>
    <select id="{$baseID}Id" name="id"{$rightSide}>
        {foreach item='message' from=$items}
            <option value="{$message.id}"{if $selectedId eq $message.id} selected="selected"{/if}>{$message->getTitleFromDisplayPattern()}</option>
        {foreachelse}
            <option value="0">{gt text='No entries found.'}</option>
        {/foreach}
    </select>
    <br{$break} />
</p>
<p>
    <label for="{$baseID}Sort"{$leftSide}>{gt text='Sort by'}:</label>
    <select id="{$baseID}Sort" name="sort"{$rightSide}>
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
    <select id="{$baseID}SortDir" name="sortdir">
        <option value="asc"{if $sortdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
        <option value="desc"{if $sortdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
    </select>
    <br{$break} />
</p>
<p>
    <label for="{$baseID}SearchTerm"{$leftSide}>{gt text='Search for'}:</label>
    <input type="text" id="{$baseID}SearchTerm" name="searchterm"{$rightSide} />
    <input type="button" id="mUNewsSearchGo" name="gosearch" value="{gt text='Filter'}" />
    <br{$break} />
</p>
<br />
<br />

<script type="text/javascript">
/* <![CDATA[ */
    document.observe('dom:loaded', function() {
        munews.itemSelector.onLoad('{{$baseID}}', {{$selectedId|default:0}});
    });
/* ]]> */
</script>
