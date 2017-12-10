'use strict';

function mUNewsCapitaliseFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.substring(1);
}

/**
 * Initialise the quick navigation form in list views.
 */
function mUNewsInitQuickNavigation() {
    var quickNavForm;
    var objectType;

    if (jQuery('.munewsmodule-quicknav').length < 1) {
        return;
    }

    quickNavForm = jQuery('.munewsmodule-quicknav').first();
    objectType = quickNavForm.attr('id').replace('mUNewsModule', '').replace('QuickNavForm', '');

    quickNavForm.find('select').change(function (event) {
        quickNavForm.submit();
    });

    var fieldPrefix = 'munewsmodule_' + objectType.toLowerCase() + 'quicknav_';
    // we can hide the submit button if we have no visible quick search field
    if (jQuery('#' + fieldPrefix + 'q').length < 1 || jQuery('#' + fieldPrefix + 'q').parent().parent().hasClass('hidden')) {
        jQuery('#' + fieldPrefix + 'updateview').addClass('hidden');
    }
}

/**
 * Toggles a certain flag for a given item.
 */
function mUNewsToggleFlag(objectType, fieldName, itemId) {
    jQuery.ajax({
        method: 'POST',
        url: Routing.generate('munewsmodule_ajax_toggleflag'),
        data: {
            ot: objectType,
            field: fieldName,
            id: itemId
        },
        success: function (data) {
            var idSuffix;
            var toggleLink;

            idSuffix = mUNewsCapitaliseFirstLetter(fieldName) + itemId;
            toggleLink = jQuery('#toggle' + idSuffix);

            if (data.message) {
                mUNewsSimpleAlert(toggleLink, Translator.__('Success'), data.message, 'toggle' + idSuffix + 'DoneAlert', 'success');
            }

            toggleLink.find('.fa-check').toggleClass('hidden', true !== data.state);
            toggleLink.find('.fa-times').toggleClass('hidden', true === data.state);
        }
    });
}

/**
 * Initialise ajax-based toggle for all affected boolean fields on the current page.
 */
function mUNewsInitAjaxToggles() {
    jQuery('.munews-ajax-toggle').click(function (event) {
        var objectType;
        var fieldName;
        var itemId;

        event.preventDefault();
        objectType = jQuery(this).data('object-type');
        fieldName = jQuery(this).data('field-name');
        itemId = jQuery(this).data('item-id');

        mUNewsToggleFlag(objectType, fieldName, itemId);
    }).removeClass('hidden');
}

/**
 * Simulates a simple alert using bootstrap.
 */
function mUNewsSimpleAlert(anchorElement, title, content, alertId, cssClass) {
    var alertBox;

    alertBox = ' \
        <div id="' + alertId + '" class="alert alert-' + cssClass + ' fade"> \
          <button type="button" class="close" data-dismiss="alert">&times;</button> \
          <h4>' + title + '</h4> \
          <p>' + content + '</p> \
        </div>';

    // insert alert before the given anchor element
    anchorElement.before(alertBox);

    jQuery('#' + alertId).delay(200).addClass('in').fadeOut(4000, function () {
        jQuery(this).remove();
    });
}

/**
 * Initialises the mass toggle functionality for admin view pages.
 */
function mUNewsInitMassToggle() {
    if (jQuery('.munews-mass-toggle').length > 0) {
        jQuery('.munews-mass-toggle').unbind('click').click(function (event) {
            jQuery('.munews-toggle-checkbox').prop('checked', jQuery(this).prop('checked'));
        });
    }
}

/**
 * Creates a dropdown menu for the item actions.
 */
function mUNewsInitItemActions(context) {
    if (context == 'view') {
        jQuery('ul.list-inline > li > a > i.tooltips').tooltip();
    }
    if (context == 'display') {
        jQuery('.btn-group-sm.item-actions').each(function (index) {
            var innerList;
            innerList = jQuery(this).children('ul.list-inline').first().detach();
            jQuery(this).append(innerList.find('a.btn'));
        });
    }
}

/**
 * Initialises image viewing behaviour.
 */
function mUNewsInitImageViewer() {
    if (typeof(magnificPopup) === 'undefined') {
        return;
    }
    jQuery('a.image-link').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        image: {
            titleSrc: 'title',
            verticalFit: true
        },
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',
            tPrev: Translator.__('Previous (Left arrow key)'),
            tNext: Translator.__('Next (Right arrow key)'),
            tCounter: '<span class="mfp-counter">%curr% ' + Translator.__('of') + ' %total%</span>'
        },
        zoom: {
            enabled: true,
            duration: 300,
            easing: 'ease-in-out'
        }
    });
}

jQuery(document).ready(function () {
    var isViewPage;
    var isDisplayPage;

    isViewPage = jQuery('.munewsmodule-view').length > 0;
    isDisplayPage = jQuery('.munewsmodule-display').length > 0;

    mUNewsInitImageViewer();

    if (isViewPage) {
        mUNewsInitQuickNavigation();
        mUNewsInitMassToggle();
        mUNewsInitItemActions('view');
        mUNewsInitAjaxToggles();
    } else if (isDisplayPage) {
        mUNewsInitItemActions('display');
        mUNewsInitAjaxToggles();
    }
});
