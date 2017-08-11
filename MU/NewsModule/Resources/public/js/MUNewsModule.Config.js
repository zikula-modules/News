'use strict';

function newsToggleShrinkSettings(fieldName) {
    var idSuffix = fieldName.replace('munewsmodule_appsettings_', '');
    jQuery('#shrinkDetails' + idSuffix).toggleClass('hidden', !jQuery('#munewsmodule_appsettings_enableShrinkingFor' + idSuffix).prop('checked'));
}

jQuery(document).ready(function() {
    jQuery('.shrink-enabler').each(function (index) {
        jQuery(this).bind('click keyup', function (event) {
            newsToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
        });
        newsToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
    });
});
