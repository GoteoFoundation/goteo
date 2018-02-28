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
 * Document ready
 */

$(function(){
    var prontoLoad = function(href, target) {
        target = target || '#admin-content';
        prontoTarget = target;
        prontoScroll = target;
        console.log('admin pronto load into', prontoTarget, href);

        $.pronto('defaults', {
            target: { title: 'title', content: prontoTarget }
        });
        $.pronto('load', href);
    };

    // Some tweaks on pronto links
    $('#main').off('click', 'a.pronto');
    $('#main').on('click', 'a.pronto', function(e){
        var href = $(this).attr('href');
        if(href.indexOf('#') === 0) return;

        var target = $(this).attr('target');
        if(!target || href.indexOf('/admin/') === 0) {
            e.preventDefault();
            prontoLoad(href, '#admin-content');
        }
    });

    // Manage GET forms with pronto as well
    $('#main').on('submit', 'form.pronto', function(e) {
        e.preventDefault();

        var action = $(this).attr('action');
        var method = $(this).attr('method').toLowerCase();
        var query = $(this).serialize()
                           .replace("+", " ")
                           // .replace("*", "%2A")
                           // .replace("%7E", "~")
                           ;
        console.log('submit', action, query, method, e);
        if(method === 'get') {
            prontoLoad(action + '?' + query, '#admin-content');
        }
    });

    // User table links
    // $('#main').off('click', 'table.model-user a');
    $('#main').on('click', 'table.model-user a', function(e) {
        e.preventDefault();
        // e.stopPropagation();

        var href = $(this).attr('href');
        if(href.indexOf('#') === 0) return;

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
        var $new = $('<tr class="active"><td id="manage-' + id + '" colspan="' + cols +'"></td></tr>').insertAfter($tr.addClass('active'));
        // Ajax load

        if(href.indexOf('?') === -1) {
            href += '?ajax';
        } else {
            href += '&ajax';
        }
        prontoLoad(href, '#manage-' + id);
    });

    // Manual initialization of collapse plugin to apply involved classes
    $('.collapsable').collapse();
    $(window).on("pronto.render", function(e){
        $('.collapsable').collapse();
    });
});

