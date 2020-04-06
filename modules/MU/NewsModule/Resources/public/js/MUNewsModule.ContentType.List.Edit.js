'use strict';

(function($) {
    $(document).ready(function () {
        $('#zikulacontentmodule_contentitem_contentData_template').change(function () {
            $('#customTemplateArea').toggleClass('hidden', 'custom' !== $(this).val());
        }).trigger('change');
    });
})(jQuery)
