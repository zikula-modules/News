var munewsmodule = function(quill, options) {
    setTimeout(function() {
        var button;

        button = jQuery('button[value=munewsmodule]');

        button
            .css('background', 'url(' + Zikula.Config.baseURL + Zikula.Config.baseURI + '/public/modules/munews/images/admin.png) no-repeat center center transparent')
            .css('background-size', '16px 16px')
            .attr('title', 'News')
        ;

        button.click(function() {
            MUNewsModuleFinderOpenPopup(quill, 'quill');
        });
    }, 1000);
};
