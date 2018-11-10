CKEDITOR.plugins.add('munewsmodule', {
    requires: 'popup',
    init: function (editor) {
        editor.addCommand('insertMUNewsModule', {
            exec: function (editor) {
                MUNewsModuleFinderOpenPopup(editor, 'ckeditor');
            }
        });
        editor.ui.addButton('munewsmodule', {
            label: 'News',
            command: 'insertMUNewsModule',
            icon: this.path.replace('scribite/CKEditor/munewsmodule', 'images') + 'admin.png'
        });
    }
});
