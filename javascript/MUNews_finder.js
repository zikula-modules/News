'use strict';

var currentMUNewsEditor = null;
var currentMUNewsInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getPopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a Xinha button.
 */
function MUNewsFinderXinha(editor, munewsURL)
{
    var popupAttributes;

    // Save editor for access in selector window
    currentMUNewsEditor = editor;

    popupAttributes = getPopupAttributes();
    window.open(munewsURL, '', popupAttributes);
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function MUNewsFinderCKEditor(editor, munewsURL)
{
    // Save editor for access in selector window
    currentMUNewsEditor = editor;

    editor.popup(
        Zikula.Config.baseURL + Zikula.Config.entrypoint + '?module=MUNews&type=external&func=finder&editor=ckeditor',
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}



var munews = {};

munews.finder = {};

munews.finder.onLoad = function (baseId, selectedId)
{
    $$('div.categoryselector select').invoke('observe', 'change', munews.finder.onParamChanged);
    $('mUNewsSort').observe('change', munews.finder.onParamChanged);
    $('mUNewsSortDir').observe('change', munews.finder.onParamChanged);
    $('mUNewsPageSize').observe('change', munews.finder.onParamChanged);
    $('mUNewsSearchGo').observe('click', munews.finder.onParamChanged);
    $('mUNewsSearchGo').observe('keypress', munews.finder.onParamChanged);
    $('mUNewsSubmit').addClassName('z-hide');
    $('mUNewsCancel').observe('click', munews.finder.handleCancel);
};

munews.finder.onParamChanged = function ()
{
    $('mUNewsSelectorForm').submit();
};

munews.finder.handleCancel = function ()
{
    var editor, w;

    editor = $F('editorName');
    if (editor === 'xinha') {
        w = parent.window;
        window.close();
        w.focus();
    } else if (editor === 'tinymce') {
        munewsClosePopup();
    } else if (editor === 'ckeditor') {
        munewsClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function getPasteSnippet(mode, itemId)
{
    var itemUrl, itemTitle, itemDescription, pasteMode;

    itemUrl = $F('url' + itemId);
    itemTitle = $F('title' + itemId);
    itemDescription = $F('desc' + itemId);
    pasteMode = $F('mUNewsPasteAs');

    if (pasteMode === '2' || pasteMode !== '1') {
        return itemId;
    }

    // return link to item
    if (mode === 'url') {
        // plugin mode
        return itemUrl;
    } else {
        // editor mode
        return '<a href="' + itemUrl + '" title="' + itemDescription + '">' + itemTitle + '</a>';
    }
}


// User clicks on "select item" button
munews.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = $F('editorName');
    if (editor === 'xinha') {
        if (window.opener.currentMUNewsEditor !== null) {
            html = getPasteSnippet('html', itemId);

            window.opener.currentMUNewsEditor.focusEditor();
            window.opener.currentMUNewsEditor.insertHTML(html);
        } else {
            html = getPasteSnippet('url', itemId);
            var currentInput = window.opener.currentMUNewsInput;

            if (currentInput.tagName === 'INPUT') {
                // Simply overwrite value of input elements
                currentInput.value = html;
            } else if (currentInput.tagName === 'TEXTAREA') {
                // Try to paste into textarea - technique depends on environment
                if (typeof document.selection !== 'undefined') {
                    // IE: Move focus to textarea (which fortunately keeps its current selection) and overwrite selection
                    currentInput.focus();
                    window.opener.document.selection.createRange().text = html;
                } else if (typeof currentInput.selectionStart !== 'undefined') {
                    // Firefox: Get start and end points of selection and create new value based on old value
                    var startPos = currentInput.selectionStart;
                    var endPos = currentInput.selectionEnd;
                    currentInput.value = currentInput.value.substring(0, startPos)
                                        + html
                                        + currentInput.value.substring(endPos, currentInput.value.length);
                } else {
                    // Others: just append to the current value
                    currentInput.value += html;
                }
            }
        }
    } else if (editor === 'tinymce') {
        html = getPasteSnippet('html', itemId);
        window.opener.tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if (editor === 'ckeditor') {
        /** to be done*/
    } else {
        alert('Insert into Editor: ' + editor);
    }
    munewsClosePopup();
};


function munewsClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// MUNews item selector for Forms
//=============================================================================

munews.itemSelector = {};
munews.itemSelector.items = {};
munews.itemSelector.baseId = 0;
munews.itemSelector.selectedId = 0;

munews.itemSelector.onLoad = function (baseId, selectedId)
{
    munews.itemSelector.baseId = baseId;
    munews.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    $('mUNewsObjectType').observe('change', munews.itemSelector.onParamChanged);

    if ($(baseId + '_catidMain') != undefined) {
        $(baseId + '_catidMain').observe('change', munews.itemSelector.onParamChanged);
    } else if ($(baseId + '_catidsMain') != undefined) {
        $(baseId + '_catidsMain').observe('change', munews.itemSelector.onParamChanged);
    }
    $(baseId + 'Id').observe('change', munews.itemSelector.onItemChanged);
    $(baseId + 'Sort').observe('change', munews.itemSelector.onParamChanged);
    $(baseId + 'SortDir').observe('change', munews.itemSelector.onParamChanged);
    $('mUNewsSearchGo').observe('click', munews.itemSelector.onParamChanged);
    $('mUNewsSearchGo').observe('keypress', munews.itemSelector.onParamChanged);

    munews.itemSelector.getItemList();
};

munews.itemSelector.onParamChanged = function ()
{
    $('ajax_indicator').removeClassName('z-hide');

    munews.itemSelector.getItemList();
};

munews.itemSelector.getItemList = function ()
{
    var baseId, pars, request;

    baseId = munews.itemSelector.baseId;
    pars = 'ot=' + baseId + '&';
    if ($(baseId + '_catidMain') != undefined) {
        pars += 'catidMain=' + $F(baseId + '_catidMain') + '&';
    } else if ($(baseId + '_catidsMain') != undefined) {
        pars += 'catidsMain=' + $F(baseId + '_catidsMain') + '&';
    }
    pars += 'sort=' + $F(baseId + 'Sort') + '&' +
            'sortdir=' + $F(baseId + 'SortDir') + '&' +
            'searchterm=' + $F(baseId + 'SearchTerm');

    request = new Zikula.Ajax.Request(
        Zikula.Config.baseURL + 'ajax.php?module=MUNews&func=getItemListFinder',
        {
            method: 'post',
            parameters: pars,
            onFailure: function(req) {
                Zikula.showajaxerror(req.getMessage());
            },
            onSuccess: function(req) {
                var baseId;
                baseId = munews.itemSelector.baseId;
                munews.itemSelector.items[baseId] = req.getData();
                $('ajax_indicator').addClassName('z-hide');
                munews.itemSelector.updateItemDropdownEntries();
                munews.itemSelector.updatePreview();
            }
        }
    );
};

munews.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = munews.itemSelector.baseId;
    itemSelector = $(baseId + 'Id');
    itemSelector.length = 0;

    items = munews.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (munews.itemSelector.selectedId > 0) {
        $(baseId + 'Id').value = munews.itemSelector.selectedId;
    }
};

munews.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = munews.itemSelector.baseId;
    items = munews.itemSelector.items[baseId];

    $(baseId + 'PreviewContainer').addClassName('z-hide');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (munews.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === munews.itemSelector.selectedId) {
                selectedElement = items[i];
                break;
            }
        }
    }

    if (selectedElement !== null) {
        $(baseId + 'PreviewContainer').update(window.atob(selectedElement.previewInfo))
                                      .removeClassName('z-hide');
    }
};

munews.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = munews.itemSelector.baseId;
    itemSelector = $(baseId + 'Id');
    preview = window.atob(munews.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    $(baseId + 'PreviewContainer').update(preview);
    munews.itemSelector.selectedId = $F(baseId + 'Id');
};
