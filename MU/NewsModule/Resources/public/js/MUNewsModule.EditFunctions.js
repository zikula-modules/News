'use strict';

/**
 * Resets the value of an upload / file input field.
 */
function mUNewsResetUploadField(fieldName) {
    jQuery('#' + fieldName).attr('type', 'input');
    jQuery('#' + fieldName).attr('type', 'file');
}

/**
 * Initialises the reset button for a certain upload input.
 */
function mUNewsInitUploadField(fieldName) {
    jQuery('#' + fieldName + 'ResetVal').click(function (event) {
        event.preventDefault();
        mUNewsResetUploadField(fieldName);
    }).removeClass('hidden');
}

/**
 * Initialises the reset button for a certain date input.
 */
function mUNewsInitDateField(fieldName) {
    jQuery('#' + fieldName + 'ResetVal').click(function (event) {
        event.preventDefault();
        if ('DIV' == jQuery('#' + fieldName).prop('tagName')) {
            jQuery('#' + fieldName + '_date, #' + fieldName + '_time').val('');
        } else {
            jQuery('#' + fieldName + ', #' + fieldName + '').val('');
        }
    }).removeClass('hidden');
}

var editedObjectType;
var editedEntityId;
var editForm;
var formButtons;
var triggerValidation = true;

function mUNewsTriggerFormValidation() {
    mUNewsExecuteCustomValidationConstraints(editedObjectType, editedEntityId);

    if (!editForm.get(0).checkValidity()) {
        // This does not really submit the form,
        // but causes the browser to display the error message
        editForm.find(':submit').first().click();
    }
}

function mUNewsHandleFormSubmit(event) {
    if (triggerValidation) {
        mUNewsTriggerFormValidation();
        if (!editForm.get(0).checkValidity()) {
            event.preventDefault();
            return false;
        }
    }

    // hide form buttons to prevent double submits by accident
    formButtons.each(function (index) {
        jQuery(this).addClass('hidden');
    });

    return true;
}

/**
 * Initialises an entity edit form.
 */
function mUNewsInitEditForm(mode, entityId) {
    if (jQuery('.munews-edit-form').length < 1) {
        return;
    }

    editForm = jQuery('.munews-edit-form').first();
    editedObjectType = editForm.attr('id').replace('EditForm', '');
    editedEntityId = entityId;

    if (jQuery('#moderationFieldsSection').length > 0) {
        jQuery('#moderationFieldsContent').addClass('hidden');
        jQuery('#moderationFieldsSection legend').addClass('pointer').click(function (event) {
            if (jQuery('#moderationFieldsContent').hasClass('hidden')) {
                jQuery('#moderationFieldsContent').removeClass('hidden');
                jQuery(this).find('i').removeClass('fa-expand').addClass('fa-compress');
            } else {
                jQuery('#moderationFieldsContent').addClass('hidden');
                jQuery(this).find('i').removeClass('fa-compress').addClass('fa-expand');
            }
        });
    }

    var allFormFields = editForm.find('input, select, textarea');
    allFormFields.change(function (event) {
        mUNewsExecuteCustomValidationConstraints(editedObjectType, editedEntityId);
    });

    formButtons = editForm.find('.form-buttons input');
    if (editForm.find('.btn-danger').length > 0) {
        editForm.find('.btn-danger').first().bind('click keypress', function (event) {
            if (!window.confirm(Translator.__('Do you really want to delete this entry?'))) {
                event.preventDefault();
            }
        });
    }
    editForm.find('button[type=submit]').bind('click keypress', function (event) {
        triggerValidation = !jQuery(this).attr('formnovalidate');
    });
    editForm.submit(mUNewsHandleFormSubmit);

    if (mode != 'create') {
        mUNewsTriggerFormValidation();
    }
}

/**
 * Initialises a relation field section with optional edit capabilities.
 */
function mUNewsInitRelationHandling(objectType, alias, idPrefix, includeEditing, inputType, createUrl) {
    if (includeEditing) {
        mUNewsInitInlineEditingButtons(objectType, alias, idPrefix, inputType, createUrl);
    }
}

