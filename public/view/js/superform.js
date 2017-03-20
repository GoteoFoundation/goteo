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
/**
 *
 * Plugin para realizar cambios Ajax en el Super form
 * La estructura html de superform es:
 *
 * <div class="superform  autoupdate">
 *
 *   <div class="elements">
 *     <ol>
 *         <li class="element" id="campo">
 *
 *             <div class="contents">
 *             ... contenido final...
 *             </div>
 *
 *             <div class="feedback" id="superform-feedback-for-campo">
 *             ... contenido feedback ...
 *             </div>
 *
 *             <div class="children">
 *                 ... estructura iterativa, se repite a partir de div.elements ...
 *                 <div class="elements">
 *                     ...
 *                 </div>
 *
 *             </div>
 *
 *         </li>
 *
 *     </ol>
 *   </div>
 *
 * </div>
 *
 *  El plugins debe buscar el elemento form en el que esta contenido
 * Ejemplos
 */
(function ( $ ) {

    /**
     * Sustituye elegantemente (slide) un elemento jquery por otro
     */
    var _superformUpdateElement = function(el, new_el) {
        // console.log(new_el.html());
        //pintado directo:
        // el.html(new_el.html()).slideDown('slow');
        // return;

        //array de promesas para lanzar el evento superform.dom.done una vez se han acabado las animaciones
        var promises = [];

        // console.log('el: ', el[0].id, ' new_el: ', new_el[0].id);

        //miramos si hay apartado div.contents
        var cont = el.children('div.contents');
        var new_cont = new_el.children('div.contents');

        //elemento que tiene el foco (se ha hecho click)
        var focused = $(':focus').first();

        //ya existe, sustituimos o borramos
        if (cont.length) {
            //console.log(cont.html());
            // console.log(new_cont.html());
            //no esta en el nuevo, borramos
            if (!new_cont.length) {
                // console.log('no hay contenido, borramos actual');
                cont.slideUp('slow', function(){
                    cont.remove();
                });
            //esta en el nuevo, sustituimos
            } else if (!focused.length || (!$.contains(cont[0], focused[0]))) {
                // console.log('REPLACE WITH', new_cont);
                cont.replaceWith(new_cont);
            }
        //no existe, añadimos
        } else if (new_cont.length) {
            el.append(new_cont);
        }

        if(new_cont.length) {
            goteo.trace('NEW CONTENT:', new_cont.html());
            // Reset select fields if value comming from AJAX is not the same as previsously selected
            var sel = new_cont.contents('select');
            var cur_sel = $('#' + sel.attr('id'));
            if(sel.is('select') && cur_sel.is('select')) {
                cur_sel.val(sel.val());
            }
        }

        //miramos si hay apartado feedback
        var feed = el.children('div.feedback');
        var new_feed = new_el.children('div.feedback');
        //si existe nuevo feedback los sustituimos
        // goteo.trace('old feedback',feed.html());
        if (new_feed.length) {
            // console.log('new',new_feed.html());
            //existe el antiguo, sustituimos
            if (feed.length) {
                feed.html(new_feed.html());
            //no existe el antiguo, añadimos
            } else {
                el.append(new_feed);
            }
        //no existe el nuevo, borramos el antiguo
        } else if (feed.length) {
            feed.remove();
        }


        // miraremos todos los hijos del objecto a pintar y los añadiremos si no estan en el elemento
        // Antiguos y nuevos elementos:
        var ol = el.children('div.children').children('div.elements').children('ol');
        var elements = ol.children('li.element');
        var new_elements = new_el.children('div.children').children('div.elements').children('ol').children('li.element');

        if (!elements.length && new_elements.length) {
            // console.log('preparamos nuevo contenido');
            el.children('div.children').remove();
            var c = $('<div class="children"><div class="elements"><ol></ol></div></div>');
            el.append(c);
            ol = el.children('div.children').children('div.elements').children('ol');
        }

        //buscamos todos los elementos nuevos
        new_elements.each(function (i, new_child) {
            $new_child = $(new_child);
            var new_child_id = $new_child.attr('id');
            var $child = elements.filter('li.element#' + new_child_id);

            if ($child.length) {
                // var new_el = $(html).find('li.element#' + new_child_id);
                // console.log('delegamos child', $new_child[0].id, ' class ', $child.attr('class'), ' new class ', $new_child.attr('class'));
                // console.log('html actual:',$child.html());
                $child.attr('class', $new_child.attr('class'));
                _superformUpdateElement($child, $new_child);
                $child.appendTo($child.parent());
            } else {
                // console.log('añadimos hijo con nuevo contenido: ', $new_child.attr('id'),$new_child.attr('class'));
                $new_child.hide();
                $new_child.appendTo(ol);
                if(!$new_child.hasClass('hidden')) {
                    var promise = $.Deferred();
                    $new_child.slideDown('slow', function(){
                        promise.resolve();
                    });
                    promises.push(promise);
                }
            }

            // console.log('i:'+i+' id: '+new_child_id, 'old',$child.html(), 'new:', $new_child.html());
        });

        //borrar elementos antiguos que no existen ya no estan en los nuevos
        elements.each(function (i, child) {
            var $child = $(child);
            if (!new_elements.filter('li.element#' + $child.attr('id')).length) {
                var promise = $.Deferred();
                $child.slideUp('slow', function () {
                    $child.remove();
                    promise.resolve();
                });
                promises.push(promise);
            }
        });

        return promises;
    };


    /**
     * Realiza la sustitucion html y lanzan los eventos
     */
    var _superformUpdate = function(t, el, html) {
        var caller = t.get(0);
        if(el.length && caller instanceof HTMLElement) {
            if( ! el.attr('id')) {
                alert('Error, not id present in the target!');
                return false;
            }

            //obtener el mismo elemento del codigo html de la respuesta
            var new_el = $(html).find('li.element#' + el.attr('id'));

            //verificar si existe dicho elemento
            if( ! new_el.attr('id')) {
                // alert('Error, the expected id ['+new_el.attr('id')+'] is not present in html response!');
                goteo.trace('superform', 'Trigger: superform.dom.error', ' element:', t, 'new_el:', new_el, 'html:', html);
                t.trigger('superform.dom.error', [html, new_el]);
                t.trigger('superform.dom.done', [html, new_el]);
                return false;
            }
            //actualizar html
            //obtener el foco por si se esta escribiendo en algun text/textarea
            var $focused = $(document.activeElement);
            // console.log($focused,$focused[0].tagName,$focused.attr('class'),$focused.attr('id'));
            var focusedElement = $focused[0].tagName.toLowerCase() + '#' + $focused.attr('id');
            // console.log(focusedElement);

            //evento de antes de actualizar
            goteo.trace('superform', 'Trigger: superform.dom.started', ' element:', t);
            t.trigger('superform.dom.started', [html, new_el]);

            var promises = _superformUpdateElement(el, new_el);

            //recuperar foco
            $(focusedElement).focus();

            //evento de despues de actualizar
            $.when.apply( $, promises ).always(function(){
                // el.removeClass('updating busy');
                el.attr('class', new_el.attr('class'));
                goteo.trace('superform', 'Trigger: superform.dom.done', ' element:', t, 'new_el:', new_el);
                t.trigger('superform.dom.done', [html, new_el]);
            });

            // console.log('update:',el.attr('id'),data);
        }
        else {
            // alert('not updating');
        }
    };

    /**
     * Envia el superform y sustituye el elemento via ajax
     * Si el parametro data es un string, se puede usar para actualizar el html del elemento target
     * @param  {[type]} options [description]
     * @return {[type]}         [description]
     */
    $.fn.superform = function( options ) {
        var t = this;

        // Si es un string no hay llamada post, actualiza el target con el string enviado
        if(typeof options === 'string') {
            _superformUpdate(t, t, options);
            return;
        }

        // This is the easiest way to have default options.
        var settings = $.extend({
            // Si es un objeto, debe contener los parametros adicionales para enviar via post
            // Si no es un objecto, no se hace el envio ajax
            data: {},
            //el objecto a sustituirle el html por el retornado por la llamada ajax
            //si es false o null, no se realizará ninguna sustitucion html, por defect el objecto que hace la llamada
            //IMPORTANTE: debe contener obligatoriamente un id unico
            //MEJORA: si se pasa por ajax solo el html necesario se podria obviar y sustituir directamente el elemento
            target: this,
            //el objecto form por defecto es el que está contenido el elemento que hace la llamada
            form: this.closest('form')

        }, options );

        // console.log(settings);
        var frm = $(settings.form);


        //si no es un formulario, no hacemos nada con este plugin
        if( ! frm.is('form') ) {
            alert('Does not seems to be a form around...');
            return;
        }

        //objecto para $.ajax
        var post = {
                type:       'POST',
                url:        frm.attr('action'),
                cache:      false
            };

        post.data = frm.serializeArray();
        post.processData = true;

        // try {
        //     //si se quiere metodo tracicional (no sube archivos):
        //     // throw new Exception();
        //     //metdo html5
        //     post.data = new FormData(frm[0]);
        //     //con este metodo jquery no hade falte que procese el formulario:
        //     post.contentType = false;
        //     post.processData = false;
        // }
        // catch(e) {
        //     //metodo serializado
        //     post.data = frm.serializeArray();
        // }

        // console.log(typeof post.data);
        //elemento a actualizar, por defecto el que realiza la llamada
        var el = $(settings.target);

        if(typeof settings.data === 'object' && settings.data !== null && (post.url)) {
            //si todavia se está realizando la llamada ajax en el elemento, salimos
            if (frm[0].xhr) {
                frm[0].xhr.abort();
                // alert('Already updating!');
            }

            //añadir los elementos pasados
            $.each(settings.data, function (k, v) {
                // try {
                //     //metodo html5
                //     post.data.append(k, v);
                // }
                // catch(e) {
                    post.data.push({
                        name: k,
                        value: v
                    });
                // }
            });

            goteo.trace('sending data:', post.data);
            //poner en el elemento html que hace la llamada una variable para impedir actualizaciones paralelas
            el.addClass('updating busy');
            //evento de antes de empezar ajax
            // goteo.trace('Trigger: superform.ajax.started');
            t.trigger('superform.ajax.started', [el]);

            frm[0].xhr = $.ajax(post).done( function(html) {
                //ajax finalizado
                goteo.trace('Trigger: superform.ajax.done html:', html);
                t.trigger('superform.ajax.done', [html, el]);
                //actualizar el nodo si target es un elemento html
                //si no hay el el id esperado, no actualizar nada
                _superformUpdate(t, el, html);
                //alert if alert div is present
                if($(html).first().hasClass('ajax-alert')) {
                    alert($(html).first().text());
                }
            }).fail( function(html, status) {
                // console.log(html,status,xhr);
                if(status !== 'abort') {
                    // alert('Error, status return not success: ' +  status);
                    goteo.trace('Error, status return not success: ' +  status);
                    goteo.trace(html,status);
                }
            });
        }
    };

}( jQuery ));

/**
 * Inicializacion de campos automaticos
 * @return {[type]} [description]
 */
$(function() {

    //Probablemente esto no deberia estar aqui pues no forma parte del plugin en si
    //
    //

    //inicializacion de datepicker
    $('div.superform').delegate('li.element input.datepicker', 'focus', function(event) {
        var input = $(event.target);
        if(input[0].__datepicker === undefined) {
            input[0].__datepicker = 1;
            // console.log(input[0]);
            if(typeof DatePickerLocale === 'undefined') {
                DatePickerLocale = {
                    days: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                    daysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                    daysMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'],
                    months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    week: []
                };
            }
            input.DatePicker({
                format: 'Y-m-d',
                date: input.val(),
                current: input.val(),
                starts: 1,
                position: 'bottom',
                eventName: 'click',
                onBeforeShow: function(){
                    input.DatePickerSetDate(input.val(), true);
                },
                onChange: function(formatted){
                        input.val(formatted);
                        input.DatePickerHide();
                        input.focus();
                },
                locale: DatePickerLocale
            });
        }


    });

    //auto escondido de feedback
    $('div.superform').delegate('li.element', 'click', function (event) {
        $(event.target).parents('li.element').each(function (i, li) {
            var id = li.id.substr(3); //li elements has "li-*" as id
            var fb = $(li).find('div.feedback#superform-feedback-for-' + id).not(':empty').first();
            // goteo.trace('search feedback for id: ',id, $(li.id).html());
            if (fb.length) {
                // goteo.trace('Found feedback for id:',id);
                setTimeout(function () {
                    $('div.superform div.feedback').not(fb).fadeOut(200);
                });
                setTimeout(function () {
                    fb.fadeIn(200);
                });

                return false;
            }

        });

    });


    // Deteccion de campos tipo FILE, si el tamaño és demasiado grande
    // @FIXME verificar tipos de campos multiple
    $('div.superform').delegate('input[type="file"]', 'change', function(event){
        var input = $(event.target);
        //2 M, tamaño máximo aunque esto seria mejor desde una variable global de configuración
        var MAX_FILE_SIZE = 2;
        //Esto solo funciona en HTML5
        try {
            //el mensaje de error deberia venir de configuraciones globales también
            if(this.files[0].size > MAX_FILE_SIZE * 1024 * 1024) {
                alert('File too big! Please try a smaller file than ' + MAX_FILE_SIZE + 'Mb.');
                input.val('');
                return false;
            }
            //auto-actualizacion si lleva la clase autoupdate
            // @TODO, poner barra de progreso

        }catch(e){}
    });

    //auto-actualizacion de elementos si el superform tiene la clase autoupdate
    //Checkboxes, radios i select
    $('div.superform.autoupdate').delegate('li.element input[type="checkbox"],li.element input[type="radio"],li.element select', 'change', function (event) {
        var input = $(event.target);
        var li = input.closest('div.superform > div.elements > ol > li.element');
        // var li = input.closest('li.element');
        // alert(li[0].id)
        if(li[0].__updating === undefined) {
            li[0].__updating = null;
        }
        clearTimeout(li[0].__updating);

        li[0].__updating = setTimeout(function () {
            li.superform();
        }, 700);
    });

    //input text i textareas
    $('div.superform.autoupdate').delegate('li.element input[type="text"],li.element textarea', 'keydown paste focus', function (event) {
        var input = $(event.target);
        // se puede evitar la auto-actualizacion de ciertos elementos
        if(input.hasClass('no-autoupdate')) return;

        //elemento padre
        var li = input.closest('div.superform > div.elements > ol > li.element');
        // elemento immediatamente superior
        // var li = input.closest('li.element');

        //definimos las variables la primera vez
        if(li[0].__updating === undefined) {

            li[0].__lastVal = input.val();
            li[0].__updating = null;

            li[0].__update = function (input, li) {
               var val = input.val();
               if (val !== li[0].__lastVal) {
                   li[0].__lastVal = val;
                   li.superform();
               } else {
                    li.removeClass('busy');
               }
            };
        }

        clearTimeout(li[0].__updating);
        if(event.type === 'keydown') {

            li[0].__updating = setTimeout(function () {
               li[0].__update(input, li);
            }, 700);
        }

        if(event.type === 'paste') {
            li[0].__update(input, li);
        }

        if(event.type === 'focusin') {

            input.one('blur', function () {
                li[0].__update(input, li);
            });

        }

    });

    //
});

