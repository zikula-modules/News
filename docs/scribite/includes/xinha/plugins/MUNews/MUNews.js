// MUNews plugin for Xinha
// developed by Michael Ueberschaer
//
// requires MUNews module (http://webdesign-in-bremen.com)
//
// Distributed under the same terms as xinha itself.
// This notice MUST stay intact for use (see license.txt).

'use strict';

function MUNews(editor) {
    var cfg, self;

    this.editor = editor;
    cfg = editor.config;
    self = this;

    cfg.registerButton({
        id       : 'MUNews',
        tooltip  : 'Insert MUNews object',
     // image    : _editor_url + 'plugins/MUNews/img/ed_MUNews.gif',
        image    : '/images/icons/extrasmall/favorites.png',
        textMode : false,
        action   : function (editor) {
            var url = Zikula.Config.baseURL + 'index.php'/*Zikula.Config.entrypoint*/ + '?module=MUNews&type=external&func=finder&editor=xinha';
            MUNewsFinderXinha(editor, url);
        }
    });
    cfg.addToolbarElement('MUNews', 'insertimage', 1);
}

MUNews._pluginInfo = {
    name          : 'MUNews for xinha',
    version       : '1.0.0',
    developer     : 'Michael Ueberschaer',
    developer_url : 'http://webdesign-in-bremen.com',
    sponsor       : 'ModuleStudio 0.6.1',
    sponsor_url   : 'http://modulestudio.de',
    license       : 'htmlArea'
};
