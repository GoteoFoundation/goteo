/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/

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
    $('.scroll-pane').jScrollPane({showArrows: true});

    $('body').addClass('js');

    if(jQuery().tipsy) {
        $('.tipsy').tipsy();
    }
    /* Rolover sobre los cuadros de color */
    $("#menu li").hover(
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

    if(jQuery().slides) {
        // Footer sponsors
        $('#slides_sponsor').slides({
            container: 'slides_container',
            effect: 'fade',
            crossfade: false,
            fadeSpeed: 350,
            play: 5000,
            pause: 1
        });
    }

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

