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
    // Close mensajes error
   $(".message-close").click(function (event) {
            $("#message").fadeOut(2000);
   });

    // VARIS
    if(jQuery().pluginName) {
        $('.scroll-pane').jScrollPane({showArrows: true});
    }

    $('body').addClass('js');
    $('.tipsy').tipsy();
    /* Rolover sobre los cuadros de color */
    $("li").hover(
            function () { $(this).addClass('active'); },
            function () { $(this).removeClass('active'); }
    );
    $('.activable').hover(
        function () { $(this).addClass('active'); },
        function () { $(this).removeClass('active'); }
    );
    $(".a-null").click(function (event) {
        event.preventDefault();
    });

    // LANG
    $("#lang").hover(function(){
       //desplegar idiomas
       try{clearTimeout(TID_LANG);}catch(e){}
       var pos = $(this).offset().left;
       $('ul.lang').css({left:pos+'px'});
       $("ul.lang").fadeIn();
       $("#lang").css("background","#808285 url(SRC_URL + '/view/css/bolita.png') 4px 7px no-repeat");

   },function() {
       TID_LANG = setTimeout(function() {$("ul.lang").hide();},100);
    });
    $('ul.lang').hover(function(){
        try{clearTimeout(TID_LANG);}catch(e){}
    },function() {
       TID_LANG = setTimeout(function() {$("ul.lang").hide();},100);
       $("#lang").css("background","#59595C url(SRC_URL + '/view/css/bolita.png') 4px 7px no-repeat");
    });

    // CURRENCY
    $("#currency").hover(function(){
       //desplegar idiomas
       try{clearTimeout(TID_CURRENCY);}catch(e){}
       var pos = $(this).offset().left;
       $('ul.currency').css({left:pos+'px'});
       $("ul.currency").fadeIn();
       $("#currency").css("background","#808285");

   },function() {
       TID_CURRENCY = setTimeout(function(){$("ul.currency").hide();},100);
    });
    $('ul.currency').hover(function(){
        try{clearTimeout(TID_CURRENCY);}catch(e){}
    },function() {
       TID_CURRENCY = setTimeout(function(){$("ul.currency").hide();},100);
       $("#currency").css("background","#59595C");
    });

    // Footer sponsors
    $('#slides_sponsor').slides({
        container: 'slides_container',
        effect: 'fade',
        crossfade: false,
        fadeSpeed: 350,
        play: 5000,
        pause: 1
    });

    // Session keeper
    goteo.keepAlive();

    try {
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
    } catch(e){}
});
