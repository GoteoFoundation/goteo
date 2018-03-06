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
    // Intial page for auto-scroll
    $container = $('.auto-update-projects');
    $in_review = $('#include-in-review').length && $('#include-in-review');
    var total = $container.data('total');
    var query = {
        filter: $container.data('filter'),
        strict: true,
        pag: 1,
        limit: $container.data('limit'),
        location: goteo.urlParams['location'],
        latitude: goteo.urlParams['latitude'],
        longitude: goteo.urlParams['longitude'],
        category: goteo.urlParams['category'],
        q: goteo.urlParams['q']
    };

    // Auto paginate on reaching bottom page
    $(window).scroll(function() {
        if($container.find('.loading-container').length) return;
        if(query.limit * query.pag >= total) return;

        var $last = $container.find('.widget-element:last');

        // console.log('windowScrollTop',$(window).scrollTop(), 'last offset', $last.offset(), 'last height', $last.height());
        if($(window).scrollTop() >= $last.offset().top - $last.height()) {
            // ajax call get data from server and append to the div
            $last.after('<div class="loading-container">' + goteo.texts['regular-loading'] + '</div>');

            // console.log('end reached, loading more. total', total, 'query', query);
            $.getJSON('/discover/ajax', query, function(result) {
                total = result.total;
                query.limit = result.limit;
                result.items.forEach(function(html, index) {
                  var $new = $('<div class="col-sm-6 col-md-4 col-xs-12 spacer widget-element">' + html + '</div>');
                  $new.hide().insertAfter($last).fadeIn();
                });
                $container.find('.loading-container').remove();
                query.pag++;
            });
        }
    });

    // if admin, allow to choose if search in-review projects
    $('#main').on('submit', 'form.form-search', function(e) {
        if($in_review) {
            // console.log('in review', $in_review.prop('checked'));
            $('<input>').attr('type', 'hidden')
              .attr('name', $in_review.attr('name'))
              .attr('value', $in_review.prop('checked') ? 1 : 0)
              .appendTo($(this));
        }
    });

    // Auto submit select in search bar
    $('#main').on('change', 'form.form-search select', function(e) {
        e.preventDefault();
        $(this).closest('form').submit();
    });
});
