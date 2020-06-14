( function ($) {
    $.extend($.summernote.plugins, {
        /**
         * @param {Object} context - context object has status of editor.
         */
        'munewsmodule': function (context) {
            var self = this;

            // ui provides methods to build ui elements.
            var ui = $.summernote.ui;

            // add button
            context.memo('button.munewsmodule', function () {
                // create button
                var button = ui.button({
                    contents: '<img src="' + Zikula.Config.baseURL + Zikula.Config.baseURI + '/public/modules/munews/images/admin.png' + '" alt="News" width="16" height="16" />',
                    tooltip: 'News',
                    click: function () {
                        MUNewsModuleFinderOpenPopup(context, 'summernote');
                    }
                });

                // create jQuery object from button instance.
                var $button = button.render();

                return $button;
            });
        }
    });
})(jQuery);
