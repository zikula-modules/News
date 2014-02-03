'use strict';


/**
 * Resets the value of an upload / file input field.
 */
function munewsResetUploadField(fieldName)
{
    if ($(fieldName) != undefined) {
        $(fieldName).setAttribute('type', 'input');
        $(fieldName).setAttribute('type', 'file');
    }
}

/**
 * Initialises the reset button for a certain upload input.
 */
function munewsInitUploadField(fieldName)
{
    if ($('reset' + fieldName.capitalize() + 'Val') != undefined) {
        $('reset' + fieldName.capitalize() + 'Val').observe('click', function (evt) {
            evt.preventDefault();
            munewsResetUploadField(fieldName);
        }).removeClassName('z-hide');
    }
}

/**
 * Resets the value of a date or datetime input field.
 */
function munewsResetDateField(fieldName)
{
    if ($(fieldName) != undefined) {
        $(fieldName).value = '';
    }
    if ($(fieldName + 'cal') != undefined) {
        $(fieldName + 'cal').update(Zikula.__('No date set.', 'module_MUNews'));
    }
}

/**
 * Initialises the reset button for a certain date input.
 */
function munewsInitDateField(fieldName)
{
    if ($('reset' + fieldName.capitalize() + 'Val') != undefined) {
        $('reset' + fieldName.capitalize() + 'Val').observe('click', function (evt) {
            evt.preventDefault();
            munewsResetDateField(fieldName);
        }).removeClassName('z-hide');
    }
}

