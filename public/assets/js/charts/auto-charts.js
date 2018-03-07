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

    var createChart = function($ob, data, settings) {
        console.log('data', data);

        console.log('createChart with', settings)

        if(!data || (data.items === undefined && !data.length) || (data.items !== undefined && !data.items.length)) {
            $ob.html('<small class="text-danger">No data</small>');
        }
        else if($ob.hasClass('percent-pie')) {

            // console.log('data', data);
            var pieData = data.map(function(x){
                return {label:x.label, value: x.counter};
            });
            var pie = d3.goteo.pieChart();
            d3.select($ob[0]).datum(pieData).call(pie);

        } else if($ob.hasClass('time-metrics')) {
            var data_transformed = data.items.map(function(x){
                x.date = new Date(x.date);
                return x;
            });
            settings.min_date = new Date(data.min_date);
            settings.max_date = new Date(data.max_date);
            var time = d3.goteo.timemetricsChart(settings);
            d3.select($ob[0]).datum(data_transformed).call(time);

        } else {
            $ob.html('<small class="text-danger">Chart not found</small>');
        }
    };

    var initBindings = function() {
        var sources = {}; // Sources cache

        // Percent pie with source
        $('.d3-chart').each(function(){
            var $self = $(this);
            if($self.hasClass('auto-enlarge')) {
                $self.css('cursor', 'pointer');
            }

            var source = $(this).data('source');

            $.getJSON(source)
                .done(function(data) {
                    sources[source] = data;
                    createChart($self, data, $self.data());
                })
                .fail(function(error) {
                    console.log(error);
                    $self.html('<small class="text-danger">' + (error || error.error) + '</small>');
                    // throw error;
                });
        });

        // Update charts from data-properties
        $('.d3-chart-updater').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var source = $(target).data('source');
            var settings = $.extend($(target).data(), $(this).data());
            console.log('reinit with', settings);
            createChart($(target), sources[source], settings);
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
