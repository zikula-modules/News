CKEDITOR.plugins.add('munewsmodule', {
    requires: 'popup',
    lang: 'en,nl,de',
    init: function (editor) {
        editor.addCommand('insertMUNewsModule', {
            exec: function (editor) {
                var url = Routing.generate('munewsmodule_external_finder', { objectType: 'message', editor: 'ckeditor' });
                // call method in MUNewsModule.Finder.js and provide current editor
                MUNewsModuleFinderCKEditor(editor, url);
            }
        });
        editor.ui.addButton('munewsmodule', {
            label: editor.lang.munewsmodule.title,
            command: 'insertMUNewsModule',
            icon: this.path.replace('scribite/CKEditor/munewsmodule', 'public/images') + 'admin.png'
        });
    }
});
