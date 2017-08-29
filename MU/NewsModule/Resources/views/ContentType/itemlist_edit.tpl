{* Purpose of this template: edit view of generic item list content type *}
<div class="form-group">
    {gt text='Object type' domain='munewsmodule' assign='objectTypeSelectorLabel'}
    {formlabel for='mUNewsModuleObjectType' text=$objectTypeSelectorLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {munewsmoduleObjectTypeSelector assign='allObjectTypes'}
        {formdropdownlist id='mUNewsModuleObjectType' dataField='objectType' group='data' mandatory=true items=$allObjectTypes cssClass='form-control'}
        <span class="help-block">{gt text='If you change this please save the element once to reload the parameters below.' domain='munewsmodule'}</span>
    </div>
</div>

{if $featureActivationHelper->isEnabled(constant('MU\\NewsModule\\Helper\\FeatureActivationHelper::CATEGORIES'), $objectType)}
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
        <div class="form-group">
            {assign var='hasMultiSelection' value=$categoryHelper->hasMultipleSelection($objectType, $propertyName)}
            {gt text='Category' domain='munewsmodule' assign='categorySelectorLabel'}
            {assign var='selectionMode' value='single'}
            {if $hasMultiSelection eq true}
                {gt text='Categories' domain='munewsmodule' assign='categorySelectorLabel'}
                {assign var='selectionMode' value='multiple'}
            {/if}
            {formlabel for="mUNewsModuleCatIds`$propertyName`" text=$categorySelectorLabel cssClass='col-sm-3 control-label'}
            <div class="col-sm-9">
                {formdropdownlist id="mUNewsModuleCatIds`$propName`" items=$categories.$propName dataField="catids`$propName`" group='data' selectionMode=$selectionMode cssClass='form-control'}
                <span class="help-block">{gt text='This is an optional filter.' domain='munewsmodule'}</span>
            </div>
        </div>
    {/foreach}
    {/nocache}
{/if}
{/formvolatile}
{/if}

<div class="form-group">
    {gt text='Sorting' domain='munewsmodule' assign='sortingLabel'}
    {formlabel text=$sortingLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formradiobutton id='mUNewsModuleSortRandom' value='random' dataField='sorting' group='data' mandatory=true}
        {gt text='Random' domain='munewsmodule' assign='sortingRandomLabel'}
        {formlabel for='mUNewsModuleSortRandom' text=$sortingRandomLabel}
        {formradiobutton id='mUNewsModuleSortNewest' value='newest' dataField='sorting' group='data' mandatory=true}
        {gt text='Newest' domain='munewsmodule' assign='sortingNewestLabel'}
        {formlabel for='mUNewsModuleSortNewest' text=$sortingNewestLabel}
        {formradiobutton id='mUNewsModuleSortDefault' value='default' dataField='sorting' group='data' mandatory=true}
        {gt text='Default' domain='munewsmodule' assign='sortingDefaultLabel'}
        {formlabel for='mUNewsModuleSortDefault' text=$sortingDefaultLabel}
    </div>
</div>

<div class="form-group">
    {gt text='Amount' domain='munewsmodule' assign='amountLabel'}
    {formlabel for='mUNewsModuleAmount' text=$amountLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formintinput id='mUNewsModuleAmount' dataField='amount' group='data' mandatory=true maxLength=2 cssClass='form-control'}
    </div>
</div>

<div class="form-group">
    {gt text='Template' domain='munewsmodule' assign='templateLabel'}
    {formlabel for='mUNewsModuleTemplate' text=$templateLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {munewsmoduleTemplateSelector assign='allTemplates'}
        {formdropdownlist id='mUNewsModuleTemplate' dataField='template' group='data' mandatory=true items=$allTemplates cssClass='form-control'}
    </div>
</div>

<div id="customTemplateArea" class="form-group"{* data-switch="mUNewsModuleTemplate" data-switch-value="custom"*}>
    {gt text='Custom template' domain='munewsmodule' assign='customTemplateLabel'}
    {formlabel for='mUNewsModuleCustomTemplate' text=$customTemplateLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formtextinput id='mUNewsModuleCustomTemplate' dataField='customTemplate' group='data' mandatory=false maxLength=80 cssClass='form-control'}
        <span class="help-block">{gt text='Example' domain='munewsmodule'}: <em>itemlist_[objectType]_display.html.twig</em></span>
    </div>
</div>

<div class="form-group">
    {gt text='Filter (expert option)' domain='munewsmodule' assign='filterLabel'}
    {formlabel for='mUNewsModuleFilter' text=$filterLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formtextinput id='mUNewsModuleFilter' dataField='filter' group='data' mandatory=false maxLength=255 cssClass='form-control'}
        <span class="help-block">{gt text='Example' domain='munewsmodule'}: <em>tbl.age >= 18</em></span>
    </div>
</div>

<script type="text/javascript">
    (function($) {
    	$('#mUNewsModuleTemplate').change(function() {
    	    $('#customTemplateArea').toggleClass('hidden', $(this).val() != 'custom');
	    }).trigger('change');
    })(jQuery)
</script>
