CKEDITOR.plugins.add( 'switchtoolbar', {
    init: function( editor ) {


        editor.addCommand( 'switchtoolbar', {
            exec: function( editor ) {
                if(editor.config.configname == "lightConfig"){
                }
            }
        });

        editor.ui.addButton( 'Switch', {
            label: "Changer la barre d'outils",
            command: 'switchtoolbar',
            toolbar: 'switch',
            icon: this.path + 'icons/switchtoolbar.png'
        });
    }
});