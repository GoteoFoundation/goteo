//Main goteo object
var goteo = { debug : false };

/**
 * Console debug function on non LIVE sites
 * @param {string} msg description
 */
goteo.trace = function () {
    try {
        if(goteo.debug) {
            console.log([].slice.apply(arguments));
        }
    }catch(e){}
};

/**
 * Keeps the user session open for a while
 */
goteo.keepAlive = function () {
    //llamar al identificador de sesion
    $.getJSON('/json/keepAlive',function(data){
        if(data.logged) {
            //do nothing...
            // goteo.trace(data.userid);
            setTimeout(goteo.keepAlive,30000);
        }
        if(data.info) {
            //session expired
            if(data.expires <= 0) {
                // alert(data.info);
                // location.reload();
            }
            else
                if(confirm(data.info)) {
                    location.reload();
                }
        }
    });

};

$(function() {
    goteo.keepAlive();
});


$(function() {
    // Lanza wysiwyg contenido
    CKEDITOR.replaceAll('ckeditor-text', {
        toolbar: 'Full',
        toolbar_Full: [
                ['Source','-'],
                ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
                ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
                '/',
                ['Bold','Italic','Underline','Strike'],
                ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                ['Link','Unlink','Anchor'],
                ['Image','Format','FontSize'],
              ],
        skin: 'kama',
        language: 'es',
        height: '300px',
        width: '800px'
    });
});
