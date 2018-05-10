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

function adminProntoLoad (href, target) {
    target = target || '#admin-content';
    return prontoLoad(href, target);
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

    // User table links
    $('#main').on('click', 'table.model-user tr:not(.extra) a', function(e) {

        // Skip links with target attribute or href to hashes only
        var href = $(this).attr('href');
        if(href.indexOf('#') === 0) return;
        if(href.indexOf('mailto:') === 0) return;
        if($(this).attr('target'))  return;

        e.preventDefault();
        // e.stopPropagation();

        var $tb = $(this).closest('table');
        var $tr = $(this).closest('tr');
        var id = $tr.attr('id');
        var cols = $tr.contents('td').length;
        if($('#manage-' +  id).length) {
            $tr.removeClass('active');
            $('#manage-' +  id).slideUp(function(){
                $(this).remove();
            });
            return;
        }
        var $new = $('<tr class="extra active"><td id="manage-' + id + '" colspan="' + cols +'"></td></tr>').insertAfter($tr.addClass('active'));
        // Ajax load

        var add = href.indexOf('?') === -1 ? '?' : '&';
        // console.log(add, href, href.indexOf(add));
        if(location.search.indexOf(add + 'ajax') === -1) {
            href += add + 'ajax';
            add = '&';
        }
        // Add query string
        if(location.search) href += add + location.search.substr(1);
        // console.log(href, location);
        adminProntoLoad(href, '#manage-' + id);
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
                        defaults: true // Show a list of prefetch projects without typing
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

