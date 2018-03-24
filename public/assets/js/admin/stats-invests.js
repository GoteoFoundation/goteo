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

$(function(){

    // $('.admin-typeahead').on('typeahead:select', function(event, datum, name) {
    //     console.log('search for', event, datum, name);
    //     adminProntoLoad(location.pathname + '?' + name + '=' + datum.id + '&text=' + datum.name );
    // });

    var printRaisedGraph = function(target) {
        var hash = location.hash.substr(1).split(',');
        // from hash
        var method = hash[1] || $('#tab-' + target).find('div.choose-method a:first').attr('href').substr(1);
        var interval = hash[2] || $('#tab-' + target).find('div.choose-interval a:first').attr('href').substr(1);
        if($(this).closest('div.btn-group').length) {
            var part = $(this).attr('href').substr(1);
            var is_method = $(this).closest('div.btn-group').hasClass('choose-method');
            if(is_method) method = part;
            else          interval = part;
        }
        console.log('click', hash, 'part', part, 'target', target, method, interval);

        location.hash = target  + ',' + method  + ',' + interval;
        $('#tab-' + target).find('div.choose-method a[href="#' + method +'"]').addClass('active').siblings().removeClass('active');
        $('#tab-' + target).find('div.choose-interval a[href="#' + interval +'"]').addClass('active').siblings().removeClass('active');

        var $template =  $('#tab-' + target).find('script#template-' + method + '-' + interval + '');
        var $body = $('#tab-' + target).find('.panel-body');
        // console.log($template.html(), e.target);
        // Create graph
        if($template.length) {
            console.log('Found template', method, interval, $template.html());
            $body.html($template.html());
            $(window).trigger("autocharts.init");
        } else {
            $body.html('Template [' + method + '-' + interval + '] not found!');
        }
    };

    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
      // e.target // newly activated tab
      // e.relatedTarget // previous active tab
      var target = $(e.target).attr('href').substr(1);
      var $menu = $('#tab-' + target).find('div.btn-group');
      $menu.contents('a').on('click', function(e) {
        e.preventDefault();
        printRaisedGraph.call(e.target, target);
      });
      printRaisedGraph(target);
      // $menu.contents('a:first').click();

      // var method = $menu1.attr('href').split(',');
      // var method = parts[1] || menu[1] || 'global';
      // var interval = parts[2] || menu[2] || 'today';
      // console.log('target',target,parts,'menu', $menu, $menu.attr('href'), interval)
      // var $template = $('#tab-' + target).find('script[class="' + interval + '"]');

    });


    // Click the first
    var hash = location.hash.substr(1).split(',') || [];
    if($('#tab-menu-' + hash[0]).length) {
        $('#tab-menu-' + hash[0]).click();
    } else {
        $('a[data-toggle="tab"]:first').click();
    }

});
