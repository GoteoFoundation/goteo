/**
 *
 * Plugin para realizar cambios Ajax en el Super form
 * metodos: update y updateElement
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

        // This is the easiest way to have default options.
        var settings = $.extend({
            // Si es un objeto, debe contener los parametros adicionales para enviar via post
            // Si no es un objecto, no se hace el envio ajax
            // Si es un string, actualiza el target con el string enviado
            data: null,
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
        var t = this;
        var caller = t.get(0);

        //si no es un formulario, no hacemos nada con este plugin
        if( ! frm.is('form') ) {
            alert('Does not seems to be a form around...');
            return;
        }

        var data = frm.serializeArray();
        var action = frm.attr('action');
        //elemento a actualizar
        var el = $(settings.target);

        if(typeof settings.data === 'object' && settings.data !== null && (action)) {
            //si todavia se está realizando la llamada ajax en el elemento, salimos
            if (caller.superform_updating) {
                alert('Already updating!');
                // caller.superform_updating.abort();
                return;
            }

            //añadir los elementos pasados
            $.each(settings.data, function (k, v) {
                data.push({
                    name: k,
                    value: v
                });
            });

            //poner en el elemento html que hace la llamada una variable para impedir actualizaciones paralelas
            caller.superform_updating = $.post(action, data, function(html, status) {
                //actualizar el nodo si target es un elemento html
                if(status !== 'success') {
                    alert('Error, status return not success: ' +  status);
                }
                if(el.length && el.get(0) instanceof HTMLElement) {
                    if( ! el.attr('id')) {
                        //cuando se envie por ajax solo el contenido (y no toda la pagina entera)
                        //se puede saltar este paso i sustituir el elemnto por el codigo pasado
                        alert('Error, not id present in the target!');
                    }
                    else {
                        //actualizar html
                        var new_el = $(html).find('#' + el.attr('id'));
                        _superformUpdateElement(el, new_el);

                    }
                    // console.log('update:',el.attr('id'),data);
                }
                else {
                    // alert('not updating');
                }
                caller.superform_updating = null;
            });
        }
        else if(typeof settings.data === 'string') {
            if(el.length && el.get(0) instanceof HTMLElement) {
                _superformUpdateElement(el, data);
            }
        }
    };

    /**
     * Sustituye elegantemente (slide) un pedazo de codigo html en la pagina
     */
    var _superformUpdateElement = function(old_el, new_el) {
        console.log(new_el.html());
        //pintado directo:
        // old_el.html(new_el.html()).slideDown('slow');

        //
        //

        //
        // miraremos todos los hijos del objecto a pintar y los añadiremos si no estan en el elemento
        // Antiguos y nuevos elementos:
        var old_elements = old_el.children('div.children').children('div.elements').children('ol').children('li.element');
        var new_elements = new_el.children('div.children').children('div.elements').children('ol').children('li.element');

        //buscamos todos los elementos nuevos
        new_elements.each(function (i, new_child) {
            $new_child = $(new_child);
            var new_child_id = $new_child.attr('id');
            var $old_child = old_elements.filter('li.element#' + new_child_id);

            //si el nuevo elemento existe, lo sustituimos
            if($old_child.length) {
                $old_child.html($new_child.html()).slideUp('slow').slideDown('slow');
            }
            //si no existe lo añadimos y los mostramos con slidedown
            else {
                $new_child.hide();
                if(i > 0) {
                    //añadir el elemento en la posicion que toca
                    $new_child.insertAfter( old_elements.filter(':eq(' + (i-1) + ')') );
                }
                else {
                    //no hay ningun elemento, añadir al principio
                    $new_child.prependTo( old_elements.parent() );
                }
                //mostrar el elemento "graciosamente"
                $new_child.slideDown('slow');
            }

            //borrar elementos antiguos que no existen ya no estan en los nuevos
            old_elements.each(function (i, old_child) {
                var $old_child = $(old_child);
                if (!new_elements.filter('li.element#' + $old_child.attr('id')).length) {
                    $old_child.slideUp('slow', function () {
                        $old_child.remove();
                    });
                }
            });

            console.log('i:'+i+' id: '+new_child_id, 'old',$old_child.html(), 'new:', $new_child.html());
        });

    };

}( jQuery ));