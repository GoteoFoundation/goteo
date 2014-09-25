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
     * Envia el superform y sustituye el elemento via ajax
     * Si el parametro data es un string, se puede usar para actualizar el html del elemento target
     * @param  {[type]} options [description]
     * @return {[type]}         [description]
     */
    $.fn.superform = function( options ) {
        var t = this;
        var caller = t.get(0);

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

        // Greenify the collection based on the settings variable.
        // console.log(settings);
        var frm = $(settings.form);


        //si no es un formulario, no hacemos nada con este plugin
        if( ! frm.is('form') ) {
            alert('Does not seems to be a form around...');
            return;
        }

        var data = frm.serializeArray();
        var action = frm.attr('action');
        //elemento a actualizar, por defecto el que realiza la llamada
        var el = $(settings.target);

        if(typeof settings.data === 'object' && settings.data !== null && (action)) {
            //si todavia se está realizando la llamada ajax en el elemento, salimos
            if (frm[0].xhr) {
                frm[0].xhr.abort();
                // alert('Already updating!');
            }

            //añadir los elementos pasados
            $.each(settings.data, function (k, v) {
                data.push({
                    name: k,
                    value: v
                });
            });

            // console.log('sending data:', data);
            //poner en el elemento html que hace la llamada una variable para impedir actualizaciones paralelas
            el.addClass('updating busy');
            //evento de antes de empezar ajax
            t.trigger('superform.ajax.started', [el]);

            // console.log(frm[0].id, data);

            frm[0].xhr = $.ajax({
                type:       'POST',
                url:        action,
                cache:      false,
                data:       data
            }).done( function(html, status, xhr) {
                //ajax finalizado
                t.trigger('superform.ajax.done', [html, el]);
                //actualizar el nodo si target es un elemento html
                _superformUpdate(t, el, html);
            }).fail( function(html, status, xhr) {
                // console.log(status);
                if(status != 'abort') alert('Error, status return not success: ' +  status);
            });
        }
    };

    /**
     * Realiza la sustitucion html y lanzan los eventos
     */
    var _superformUpdate = function(t, el, html) {
        var caller = t.get(0);
        if(el.length && caller instanceof HTMLElement) {
            if( ! el.attr('id')) {
                alert('Error, not id present in the target!');
            }
            else {
                //actualizar html
                var new_el = $(html).find('li.element#' + el.attr('id'));

                //evento de antes de actualizar
                t.trigger('superform.dom.started', [html, new_el]);

                var promises = _superformUpdateElement(el, new_el);

                //evento de despues de actualizar
                $.when.apply( $, promises ).always(function(){
                    // el.removeClass('updating busy');
                    el.attr('class', new_el.attr('class'));
                    t.trigger('superform.dom.done', [html, new_el]);
                });

            }
            // console.log('update:',el.attr('id'),data);
        }
        else {
            // alert('not updating');
        }
    };
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
                cont.replaceWith(new_cont);
            }
        //no existe, añadimos
        } else if (new_cont.length) {
            el.append(new_cont);
        }

        // if(new_cont.length) console.log('nuevo contendido:', new_cont.html());

        //miramos si hay apartado feedback
        var feed = el.children('div.feedback');
        var new_feed = new_el.children('div.feedback');
        //si existe nuevo feedback los sustituimos
        // console.log('old',feed.html());
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

}( jQuery ));

/**
 * Inicializacion de campos automaticos
 * @return {[type]} [description]
 */
$(function() {

    //Probablemente esto no deberia estar aqui pues no forma parte del plugin en si
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
                onChange: function(formatted, dates){
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

            var fb = $(li).find('div.feedback#superform-feedback-for-' + li.id).not(':empty').first();
            if (fb.length) {
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

    $('div.superform.autoupdate').delegate('li.element input[type="radio"],li.element select', 'click', function (event) {
        var input = $(event.target);
        var li = input.closest('li.group');
        $(this).closest('li.group').first().find('input[type="radio"][name="' + input.attr('name') + '"]').each(function(i, r){
            try {
              if (input.attr('id') == r.id) {
                  $('div.children#' + r.id + '-children').slideDown(400);
              } else {
                  $('div.children#' + r.id + '-children').slideUp(400);
              }
            } catch (e) {}
        });

    });
    //input text i textareas
    $('div.superform.autoupdate').delegate('li.element input[type="text"],li.element textarea', 'keydown paste focus', function (event) {

        var input = $(event.target);
        var li = input.closest('div.superform > div.elements > ol > li.element');
        // var li = input.closest('li.element');

        //definimos las variables la primera vez
        if(li[0].__updating === undefined) {

            li[0].__lastVal = input.val();
            li[0].__updating = null;

            li[0].__update = function (input, li) {
               var val = input.val();
               if (val != li[0].__lastVal) {
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
