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

    var initBindings = function() {

        // Percent pie with source
        $('.d3-chart.percent-pie').each(function(){
            var $self = $(this);
            $(this).css('cursor', 'pointer');
            // console.log('source', $(this).data('source'));
            d3.json($(this).data('source'), function (error, data) {
                if(error) {
                    console.log(error);
                    $self.html('<small class="text-danger">' + (error && error.message) + '</small>');
                    throw error;
                }
                // console.log('data', data);
                var pieData = data.map(function(x){
                    return {label:x.label, value: x.counter};
                });
                var pie = d3.goteo.piechart();
                d3.select($self[0]).datum(pieData).call(pie);
            });
        });

        // enlarge charts
        $('.d3-chart.auto-enlarge').on('click', function(e) {
            e.preventDefault();
            var $wrap = $(this).closest('.chart-wrapper');
            $wrap.toggleClass('d3-chart-wide');
        });
    };

    initBindings();
    $(window).on("pronto.render", function(e){
        initBindings();
    });

});
