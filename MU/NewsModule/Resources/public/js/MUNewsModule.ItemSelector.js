'use strict';

var mUNewsModule = {};

mUNewsModule.itemSelector = {};
mUNewsModule.itemSelector.items = {};
mUNewsModule.itemSelector.baseId = 0;
mUNewsModule.itemSelector.selectedId = 0;

mUNewsModule.itemSelector.onLoad = function (baseId, selectedId)
{
    mUNewsModule.itemSelector.baseId = baseId;
    mUNewsModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#mUNewsModuleObjectType').change(mUNewsModule.itemSelector.onParamChanged);

    jQuery('#' + baseId + '_catidMain').change(mUNewsModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + '_catidsMain').change(mUNewsModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'Id').change(mUNewsModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(mUNewsModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(mUNewsModule.itemSelector.onParamChanged);
    jQuery('#mUNewsModuleSearchGo').click(mUNewsModule.itemSelector.onParamChanged);
    jQuery('#mUNewsModuleSearchGo').keypress(mUNewsModule.itemSelector.onParamChanged);

    mUNewsModule.itemSelector.getItemList();
};

mUNewsModule.itemSelector.onParamChanged = function ()
{
    jQuery('#ajaxIndicator').removeClass('hidden');

    mUNewsModule.itemSelector.getItemList();
};

mUNewsModule.itemSelector.getItemList = function ()
{
    var baseId;
    var params;

    baseId = mUNewsModule.itemSelector.baseId;
    params = {
        ot: baseId,
        sort: jQuery('#' + baseId + 'Sort').val(),
        sortdir: jQuery('#' + baseId + 'SortDir').val(),
        q: jQuery('#' + baseId + 'SearchTerm').val()
    }
    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        params[catidMain] = jQuery('#' + baseId + '_catidMain').val();
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        params[catidsMain] = jQuery('#' + baseId + '_catidsMain').val();
    }

    jQuery.getJSON(Routing.generate('munewsmodule_ajax_getitemlistfinder'), params, function( data ) {
        var baseId;

        baseId = mUNewsModule.itemSelector.baseId;
        mUNewsModule.itemSelector.items[baseId] = data;
        jQuery('#ajaxIndicator').addClass('hidden');
        mUNewsModule.itemSelector.updateItemDropdownEntries();
        mUNewsModule.itemSelector.updatePreview();
    });
};

mUNewsModule.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = mUNewsModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = mUNewsModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.get(0).options[i] = new Option(item.title, item.id, false);
    }

    if (mUNewsModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(mUNewsModule.itemSelector.selectedId);
    }
};

mUNewsModule.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = mUNewsModule.itemSelector.baseId;
    items = mUNewsModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (mUNewsModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id == mUNewsModule.itemSelector.selectedId) {
                selectedElement = items[i];
                break;
            }
        }
    }

    if (null !== selectedElement) {
        jQuery('#' + baseId + 'PreviewContainer')
            .html(window.atob(selectedElement.previewInfo))
            .removeClass('hidden');
        mUNewsInitImageViewer();
    }
};

mUNewsModule.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = mUNewsModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id').get(0);
    preview = window.atob(mUNewsModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    mUNewsModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
    mUNewsInitImageViewer();
};

jQuery(document).ready(function() {
    var infoElem;

    infoElem = jQuery('#itemSelectorInfo');
    if (infoElem.length == 0) {
        return;
    }

    mUNewsModule.itemSelector.onLoad(infoElem.data('base-id'), infoElem.data('selected-id'));
});
