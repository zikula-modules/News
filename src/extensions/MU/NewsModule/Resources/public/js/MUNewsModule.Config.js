'use strict';

function newsToggleShrinkSettings(fieldName) {
    var idSuffix;

    idSuffix = fieldName.replace('munewsmodule_config_', '');
    jQuery('#shrinkDetails' + idSuffix).toggleClass('d-none', !jQuery('#munewsmodule_config_enableShrinkingFor' + idSuffix).prop('checked'));
}

jQuery(document).ready(function () {
    jQuery('.shrink-enabler').each(function (index) {
        jQuery(this).bind('click keyup', function (event) {
            newsToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
        });
        newsToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
    });
});
