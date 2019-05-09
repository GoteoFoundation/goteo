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

function getSearchParams(k){
    var p={};
    location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(s,k,v){ p[k]=v; });
    return k?p[k]:p;
}

function adminProntoLoad (href, target) {
    target = target || '#admin-content';
    return prontoLoad(href, target);
}

function adminOrderColumn(table, settings) {
    var $tb = $(table);
    var page = parseInt($tb.data('page')) || 0;
    var total = parseInt($tb.data('total')) || 0;
    var limit = parseInt($tb.data('limit')) || 10;
    var index = -1;

    // http://isocra.github.io/TableDnD/
    settings = $.extend({
        field: 'order',
        idValue: function(row) {  // Where to find the id value in the current row
            return $(row).find('[data-key="id"]').data('value');
        },
        apiUrl:  null // Must be a function (row will be sent as parameter)
    }, settings);

    if(!settings.onDragStart) {
        settings.onDragStart = function(tb, row) {
            index = $(row).index();
            // console.log('Dragstarted, index', index);
        };
    }
    if(!$.isFunction(settings.apiUrl)) {
        alert('Please define and apiUrl as a function in settings');
        return false;
    }
    if(!settings.onDrop) {
        settings.onDrop = function(tb, row) {
            var $td = $(row).find('[data-key="' + settings.field + '"]');
            var diff = $(row).index() - index;
            var url = settings.apiUrl(row);
            // console.log('drop', tb, 'row', row, 'diff', diff, 'Id', settings.idValue(row), 'url', url, 'td', td);
            if(!url) {
                alert('ERROR, url not found', url);
                return;
            }

            var go_page = -1;
            if($(row).next().hasClass('tDnD-tr-prev')) {
                // Needs to go back 1 page
                go_page = page - 1;
            }
            if($(row).prev().hasClass('tDnD-tr-next')) {
                // Needs to go forward 1 page
                go_page = page + 1;
            }

            $tb.find('tbody').addClass('loading-container');
            $.ajax({
                url: url,
                type: 'PUT',
                data: {value: diff}
            }).done(function(data) {
                $tb.find('tbody').removeClass('loading-container');
                if(data.message) alert(data.message);
                if(data.error) {
                    // reset table
                    alert('An error has occurred');
                    console.log('error', data);
                    adminProntoLoad(location.href);
                    return;
                } else {
                    // Load next page?
                    if(go_page > -1) {
                        // console.log('goto', go_page);
                        var href = location.href.replace('pag=' + page);
                        if(location.href.indexOf('?') === -1) href += '?';
                        href += '&pag=' + go_page;
                        adminProntoLoad(href);
                        return;
                    }
                    // reorder columns
                    var real_diff = parseInt($td.data('value')) -  parseInt(data.value) + 1;
                    // console.log('done', data, 'diff', diff, 'real_diff', real_diff, 'value',$td.data('value'));
                    // Change value for all elements element
                    $tb.find('tr').each(function(){
                        var $td = $(this).find('[data-key="' + settings.field + '"]');
                        var $t =  $td.find('span') ? $td.find('span') : $td;
                        var val = parseInt(data.value) + $(this).index() - $(row).index();
                        $td.data('value', val);
                        $t.text(val);
                    });
                }
            }).fail(function(error) {
                $tb.removeClass('loading');
                var json = JSON.parse(error.responseText);
                var txt = json && json.error;
                console.log('fail', json, txt, error);
                alert(txt ? txt : error.responseText ? error.responseText : error);
            });
        };
    }

    // console.log(settings, $tb.find('th[data-key]'));
    var $th = $tb.find('th[data-key="' + settings.field + '"]');
    // $tb.find('th[data-key="' + settings.field + '"]').append('<i class="fa fa-arrows"></i>');
    $btn = $('<button class="btn btn-sm btn-default">' + $th.text() + ' <i class="fa fa-sort"></i></button>');
    $th.html($btn);
    $btn.on('click',function(e){
        e.preventDefault();
        if($(this).hasClass('active')) {
            $(this).removeClass('active');
            $btn.removeClass('btn-danger');
            $tb.removeClass('tDnD-sorting');
            $tb.find('td[data-key="' + settings.field + '"] i.fa').remove();
            $tb.find('.tDnD-tr-next,.tDnD-tr-prev').remove();
            $tb.find(settings.dragHandle ? settings.dragHandle : 'tr').unbind('touchstart mousedown').attr('style','');
        } else {
            $tb.addClass('tDnD-sorting');
            $(this).addClass('active');
            $btn.addClass('btn-danger');
            $tb.find('td[data-key="' + settings.field + '"]').append('<i class="fa fa-arrows"></i>');
            var colspan = $tb.find('th').length;
            if(page > 0) {
                $tb.find('tbody').prepend('<tr class="tDnD-tr-prev nodrag"><td colspan="' + colspan + '"><i class="fa fa-arrow-up"></i> '  + goteo.texts['admin-prev-page'] + ' <i class="fa fa-arrow-up"></i></td></tr>');
            }
            if(total / limit > page + 1) {
                $tb.find('tbody').append('<tr class="tDnD-tr-next nodrag"><td colspan="' + colspan + '"><i class="fa fa-arrow-down"></i> '  + goteo.texts['admin-next-page'] + ' <i class="fa fa-arrow-down"></i></td></tr>');
            }
            $tb.tableDnD(settings);
        }
    });
}

goteo.typeahead_engines = goteo.typeahead_engines || {};
/**
 * Document ready
 */
$(function(){

    // Some tweaks on pronto links
    $('#main').off('click', 'a.pronto');
    $('#main').on('click', 'a.pronto', function(e) {
        var href = $(this).attr('href');
        if(href.indexOf('#') === 0) return;

        var target = $(this).attr('target');
        if(!target || href.indexOf('/admin/') === 0) {
            e.preventDefault();
            adminProntoLoad(href, '#admin-content');
        }
    });

    // Manage GET forms with pronto as well
    $('#main').on('submit', 'form.pronto', function(e) {
        e.preventDefault();

        var action = $(this).attr('action');
        var method = $(this).attr('method').toLowerCase();
        var query = decodeURIComponent($(this).serialize())
                           .replace(/\+/g, " ");
        console.log('submit', action, query, method, location, e);
        if(method === 'get') {
            if(location.hash) query += location.hash;
            adminProntoLoad(action + '?' + query, '#admin-content');
        }
    });

    /**  jQuery x-Editable plugins tweaks */
    // $.fn.editable.defaults.mode = 'inline';
    $.fn.editable.defaults.ajaxOptions = {type: "put"};
    $.fn.editable.defaults.error = function(response, newValue) {
        // console.log('editable error', response, newValue);
        if(response.responseJSON && response.responseJSON.error) return response.responseJSON.error;
        return response.responseText;
    };
    $.fn.editableform.buttons = '<button type="submit" class="btn btn-cyan btn-sm editable-submit"><i class="fa fa-check"></i></button><button type="button" class="btn btn-default btn-sm editable-cancel"><i class="fa fa-remove"></i></button>';

    // Manually create auto-Editable fields on click to allow target different elements than clicked
    $('#main').on('click', '.editable', function(e) {
        var target = $(this).data('target') || this;
        e.stopPropagation();
        e.preventDefault();
        // console.log('create editable', target);
        $(target).editable('show');
        // Disable autocomplete in password fields
        $(target).on('shown', function(e, editable) {
            // console.log('shown', e, editable);
            editable.input.$input.attr('autocomplete', 'off');
        });

    });

    var initBindings = function() {
        // Manual initialization of collapse plugin to apply involved classes
        $('.collapsable').collapse();

        // Typeahead global search
        $('.admin-typeahead .typeahead').typeahead('destroy');
        $('.admin-typeahead').each(function () {
            var $this = $(this);
            var sources = $this.data('sources').split(',');
            var extra_params = $this.data('extraParams');
            // console.log('initialize with sources', sources);
            var engines = [{
                minLength: 0,
                hint: true,
                highlight: true,
                classNames: {
                    hint: ''
                }
            }];
            sources.forEach(function(source) {
                if(goteo.typeahead_engines[source]) {
                    engines.push(goteo.typeahead_engines[source]({
                        remote_statuses: '1,2,3,4,5,6', // TODO: from data-attributes
                        defaults: true, // Show a list of prefetch projects without typing
                        extra: extra_params
                    }));
                }
            });
            $.fn.typeahead.apply($this.find('.typeahead'), engines)
                .on('typeahead:active', function (event) {
                    $(event.target).select();
                })
                .on('typeahead:asyncrequest', function (event) {
                    // console.log('async loading', event);
                    $(event.target).addClass('loading');
                })
                .on('typeahead:asynccancel typeahead:asyncreceive', function (event) {
                    $(event.target).removeClass('loading');
                });
                // typeahead:select event is done when needed.
                // For example: assets/js/admin/stats.js
                // .on('typeahead:select', function (event, datum, name) {
                //     console.log('selected',name, event, datum);
                //     if(datum.url) location.href = datum.url;
                // });
        });

    };
    initBindings();
    $(window).on("pronto.render", function(e){
        initBindings();
    });

    // Manage engine sources changes in typeahead
    $('#main').on('change', '.admin-typeahead input[type="checkbox"]', function(e){
        var t = this.name;
        var $type = $(this).closest('.admin-typeahead');
        var sources = [];
        $type.find('input[type="checkbox"]:checked').each(function(){
            sources.push(this.name);
        });
        $type.data('sources', sources.join(','));
        initBindings();
    });

    // Admin modal can load ajax pages
    $('#admin-modal').on('show.bs.modal', function (event) {
      var $button = $(event.relatedTarget); // Button that triggered the modal
      var url = $button.data('url'); // Extract info from data-* attributes
      var title = $button.data('title'); // Extract info from data-* attributes
      // console.log('modal load. url:', url, 'title:', title);
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var $modal = $(this);
      var $title = $modal.find('.modal-title');
      var $body = $modal.find('.modal-body');
        if(title) $title.text(title).show();
      else $title.hide();
        if(url) {
        $body.html('').addClass('loading');
        // Request new content
        var request = $.ajax({
            url: url,
            dataType: "json",
                success: function(resp, status, jqXHR) {
                    response  = (typeof resp === "string") ? $.parseJSON(resp) : resp;
                // console.log(response);
                var text = response.content || response;
                $body.html(text);
                $body.removeClass('loading');
                initBindings();
            },
                error: function(jqXHR, status, err) {
                var error = err;
                    if(response.responseJSON && response.responseJSON.error) error = response.responseJSON.error;
                else error = response.responseText;
                console.log('error', err, error);
                $body.html(error);
            }
        });
      }
    });
});
