{* Purpose of this template: edit view of generic item list content type *}
<div class="z-formrow">
    {gt text='Object type' domain='module_munews' assign='objectTypeSelectorLabel'}
    {formlabel for='mUNewsObjectType' text=$objectTypeSelectorLabel}
        {munewsObjectTypeSelector assign='allObjectTypes'}
        {formdropdownlist id='mUNewsOjectType' dataField='objectType' group='data' mandatory=true items=$allObjectTypes}
        <span class="z-sub z-formnote">{gt text='If you change this please save the element once to reload the parameters below.' domain='module_munews'}</span>
</div>

{formvolatile}
{if $properties ne null && is_array($properties)}
    {nocache}
    {foreach key='registryId' item='registryCid' from=$registries}
        {assign var='propName' value=''}
        {foreach key='propertyName' item='propertyId' from=$properties}
            {if $propertyId eq $registryId}
                {assign var='propName' value=$propertyName}
            {/if}
        {/foreach}
        <div class="z-formrow">
            {modapifunc modname='MUNews' type='category' func='hasMultipleSelection' ot=$objectType registry=$propertyName assign='hasMultiSelection'}
            {gt text='Category' domain='module_munews' assign='categorySelectorLabel'}
            {assign var='selectionMode' value='single'}
            {if $hasMultiSelection eq true}
                {gt text='Categories' domain='module_munews' assign='categorySelectorLabel'}
                {assign var='selectionMode' value='multiple'}
            {/if}
            {formlabel for="mUNewsCatIds`$propertyName`" text=$categorySelectorLabel}
                {formdropdownlist id="mUNewsCatIds`$propName`" items=$categories.$propName dataField="catids`$propName`" group='data' selectionMode=$selectionMode}
                <span class="z-sub z-formnote">{gt text='This is an optional filter.' domain='module_munews'}</span>
        </div>
    {/foreach}
    {/nocache}
{/if}
{/formvolatile}

<div class="z-formrow">
    {gt text='Sorting' domain='module_munews' assign='sortingLabel'}
    {formlabel text=$sortingLabel}
    <div>
        {formradiobutton id='mUNewsSortRandom' value='random' dataField='sorting' group='data' mandatory=true}
        {gt text='Random' domain='module_munews' assign='sortingRandomLabel'}
        {formlabel for='mUNewsSortRandom' text=$sortingRandomLabel}
        {formradiobutton id='mUNewsSortNewest' value='newest' dataField='sorting' group='data' mandatory=true}
        {gt text='Newest' domain='module_munews' assign='sortingNewestLabel'}
        {formlabel for='mUNewsSortNewest' text=$sortingNewestLabel}
        {formradiobutton id='mUNewsSortDefault' value='default' dataField='sorting' group='data' mandatory=true}
        {gt text='Default' domain='module_munews' assign='sortingDefaultLabel'}
        {formlabel for='mUNewsSortDefault' text=$sortingDefaultLabel}
    </div>
</div>

<div class="z-formrow">
    {gt text='Amount' domain='module_munews' assign='amountLabel'}
    {formlabel for='mUNewsAmount' text=$amountLabel}
        {formintinput id='mUNewsAmount' dataField='amount' group='data' mandatory=true maxLength=2}
</div>

<div class="z-formrow">
    {gt text='Template' domain='module_munews' assign='templateLabel'}
    {formlabel for='mUNewsTemplate' text=$templateLabel}
        {munewsTemplateSelector assign='allTemplates'}
        {formdropdownlist id='mUNewsTemplate' dataField='template' group='data' mandatory=true items=$allTemplates}
</div>

<div id="customTemplateArea" class="z-formrow z-hide">
    {gt text='Custom template' domain='module_munews' assign='customTemplateLabel'}
    {formlabel for='mUNewsCustomTemplate' text=$customTemplateLabel}
        {formtextinput id='mUNewsCustomTemplate' dataField='customTemplate' group='data' mandatory=false maxLength=80}
        <span class="z-sub z-formnote">{gt text='Example' domain='module_munews'}: <em>itemlist_[objecttype]_display.tpl</em></span>
</div>

<div class="z-formrow z-hide">
    {gt text='Filter (expert option)' domain='module_munews' assign='filterLabel'}
    {formlabel for='mUNewsFilter' text=$filterLabel}
        {formtextinput id='mUNewsFilter' dataField='filter' group='data' mandatory=false maxLength=255}
        <span class="z-sub z-formnote">
            ({gt text='Syntax examples'}: <kbd>name:like:foobar</kbd> {gt text='or'} <kbd>status:ne:3</kbd>)
        </span>
</div>

{pageaddvar name='javascript' value='prototype'}
<script type="text/javascript">
/* <![CDATA[ */
    function munewsToggleCustomTemplate() {
        if ($F('mUNewsTemplate') == 'custom') {
            $('customTemplateArea').removeClassName('z-hide');
        } else {
            $('customTemplateArea').addClassName('z-hide');
        }
    }

    document.observe('dom:loaded', function() {
        munewsToggleCustomTemplate();
        $('mUNewsTemplate').observe('change', function(e) {
            munewsToggleCustomTemplate();
        });
    });
/* ]]> */
</script>
