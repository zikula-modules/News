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

    var quickNavFilterTimer;
    quickNavForm.find('select').change(function (event) {
        clearTimeout(quickNavFilterTimer);
        quickNavFilterTimer = setTimeout(function() {
            quickNavForm.submit();
        }, 5000);
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
        }
    }).done(function (data) {
        var idSuffix;
        var toggleLink;

        idSuffix = mUNewsCapitaliseFirstLetter(fieldName) + itemId;
        toggleLink = jQuery('#toggle' + idSuffix);

        /*if (data.message) {
            mUNewsSimpleAlert(toggleLink, Translator.__('Success'), data.message, 'toggle' + idSuffix + 'DoneAlert', 'success');
        }*/

        toggleLink.find('.fa-check').toggleClass('hidden', true !== data.state);
        toggleLink.find('.fa-times').toggleClass('hidden', true === data.state);
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
    if ('view' === context) {
        jQuery('ul.list-inline > li > a > i.tooltips').tooltip();
    }
    if ('display' === context) {
        jQuery('.btn-group-sm.item-actions').each(function (index) {
            var innerList;
            innerList = jQuery(this).children('ul.list-inline').first().detach();
            jQuery(this).append(innerList.find('a.btn'));
        });
    }
}

/**
 * Helper function to create new dialog window instances.
 * Note we use jQuery UI dialogs instead of Bootstrap modals here
 * because we want to be able to open multiple windows simultaneously.
 */
function mUNewsInitInlineWindow(containerElem) {
    var newWindowId;
    var modalTitle;

    // show the container (hidden for users without JavaScript)
    containerElem.removeClass('hidden');

    // define name of window
    newWindowId = containerElem.attr('id') + 'Dialog';

    containerElem.unbind('click').click(function (event) {
        event.preventDefault();

        // check if window exists already
        if (jQuery('#' + newWindowId).length < 1) {
            // create new window instance
            jQuery('<div>', { id: newWindowId })
                .append(
                    jQuery('<iframe width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto">')
                        .attr('src', containerElem.attr('href'))
                )
                .dialog({
                    autoOpen: false,
                    show: {
                        effect: 'blind',
                        duration: 1000
                    },
                    hide: {
                        effect: 'explode',
                        duration: 1000
                    },
                    title: containerElem.data('modal-title'),
                    width: 600,
                    height: 400,
                    modal: false
                });
        }

        // open the window
        jQuery('#' + newWindowId).dialog('open');
    });

    // return the dialog selector id;
    return newWindowId;
}

/**
 * Initialises modals for inline display of related items.
 */
function mUNewsInitQuickViewModals() {
    jQuery('.munews-inline-window').each(function (index) {
        mUNewsInitInlineWindow(jQuery(this));
    });
}

/**
 * Initialises image viewing behaviour.
 */
function mUNewsInitImageViewer() {
    var scripts;
    var magnificPopupAvailable;

    // check if magnific popup is available
    scripts = jQuery('script');
    magnificPopupAvailable = false;
    jQuery.each(scripts, function (index, elem) {
        if (elem.hasAttribute('src')) {
            elem = jQuery(elem);
            if (-1 !== elem.attr('src').indexOf('jquery.magnific-popup')) {
                magnificPopupAvailable = true;
            }
        }
    });
    if (!magnificPopupAvailable) {
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

/**
 * Initialises reordering view entries using drag n drop.
 */
function mUNewsInitSortable() {
    if (jQuery('#sortableTable').length < 1) {
        return;
    }

    jQuery('#sortableTable > tbody').sortable({
        cursor: 'move',
        handle: '.sort-handle',
        items: '.sort-item',
        placeholder: 'ui-state-highlight',
        tolerance: 'pointer',
        sort: function (event, ui) {
            ui.item.addClass('active-item-shadow');
        },
        stop: function (event, ui) {
            ui.item.removeClass('active-item-shadow');
        },
        update: function (event, ui) {
            jQuery.ajax({
                method: 'POST',
                url: Routing.generate('munewsmodule_ajax_updatesortpositions'),
                data: {
                    ot: jQuery('#sortableTable').data('object-type'),
                    identifiers: jQuery(this).sortable('toArray', { attribute: 'data-item-id' }),
                    min: jQuery('#sortableTable').data('min'),
                    max: jQuery('#sortableTable').data('max')
                }
            }).done(function (data) {
                /*if (data.message) {
                    mUNewsSimpleAlert(jQuery('#sortableTable'), Translator.__('Success'), data.message, 'sortingDoneAlert', 'success');
                }*/
                window.location.reload();
            });
        }
    });
    jQuery('#sortableTable').disableSelection();
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
        mUNewsInitSortable();
    } else if (isDisplayPage) {
        mUNewsInitItemActions('display');
        mUNewsInitAjaxToggles();
    }

    mUNewsInitQuickViewModals();
});
