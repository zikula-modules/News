CKEDITOR.plugins.add('MUNews', {
    requires: 'popup',
    lang: 'en,nl,de',
    init: function (editor) {
        editor.addCommand('insertMUNews', {
            exec: function (editor) {
                var url = Zikula.Config.baseURL + Zikula.Config.entrypoint + '?module=MUNews&type=external&func=finder&editor=ckeditor';
                // call method in MUNews_Finder.js and also give current editor
                MUNewsFinderCKEditor(editor, url);
            }
        });
        editor.ui.addButton('munews', {
            label: 'Insert MUNews object',
            command: 'insertMUNews',
         // icon: this.path + 'images/ed_munews.png'
            icon: '/images/icons/extrasmall/favorites.png'
        });
    }
});
