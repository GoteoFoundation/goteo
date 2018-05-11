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
        // console.log('data', data);

        // console.log('createChart with', settings)

        if(!data || (data.items === undefined && !Object.keys(data).length) || (data.items !== undefined && !data.items.length)) {
            $ob.html('<small class="text-danger">No data</small>');
        }
        else if($ob.hasClass('discrete-values')) {
            // Simple graph with discrete values

            var discrete = d3.goteo.discretevaluesChart(settings);
            d3.select($ob[0]).datum(data).call(discrete);

        } else if($ob.hasClass('percent-pie')) {

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
        var first = true;

        var initChart = function() {
            first = false;
            var $chart = $(this);
            var source = $chart.data('source');
            // console.log('initChart with source', source);
            if(!sources[source]) {
                sources[source] = {settings: $chart.data(), data: []};
            }
            var interval = parseInt(sources[source].settings.interval, 10) || 0;
            var interval_delay = parseInt(sources[source].settings['interval-delay'], 10) || 0;
            $.getJSON(source)
            .done(function(data) {
                sources[source].data = data;
                createChart($chart, data, $chart.data());
                if(interval) {
                    console.log('timeout at ', interval, 'delay at', interval_delay);
                    setTimeout(function() {
                        if($('[data-source="' +  source +'"]').length) {
                            // console.log('recreating chart with', source, sources[source]);
                            initChart.call($chart);
                        } else {
                            // console.log('canceling timeout for ', source, sources[source]);
                        }
                        }, ((first ? interval_delay : 0) + interval) * 1000);
                    }
                })
                .fail(function(error) {
                    console.log('Error fetching ', source,'ERROR:',error);
                    $chart.html('<small class="text-danger">' + ((error.responseJSON && error.responseJSON.error) || error.responseText || error) + '</small>');
                    // throw error;
                })
                .always(function() {
                    $chart.removeClass('loading');
                });
            };


        // Charts with data-source attribute
        $('.d3-chart').each(initChart);

        // Update charts from data-properties
        $('.d3-chart-updater').off('click');
        $('.d3-chart-updater').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var source = $(target).data('source');
            sources[source].settings = $.extend(sources[source].settings, $(this).data());
            // console.log('reinit with', sources[source].settings);
            createChart($(target), sources[source].data, sources[source].settings);
        });

        // Update settings from checkboxes will update data if active or remove otherwise
        $('input[type="checkbox"].d3-chart-updater').off('change');
        $('input[type="checkbox"].d3-chart-updater').on('change', function(e){
            var settings = $(this).data();
            var target = $(this).data('target');
            var source = $(target).data('source');
            if($(this).prop('checked')) {
                // add settings
                // console.log('add settings', settings);
                // Save current settings
                $(this).data('settings-backup', sources[source].settings);
                sources[source].settings = $.extend(sources[source].settings, settings);
            } else if($(this).data('settings-backup')) {
                // remove settings
                // console.log('remove settings', settings);
                // Retrieve backup
                sources[source].settings = $(this).data('settings-backup');
            }
            initChart.call($(target));
        });

        // enlarge charts
        $('.d3-chart.auto-enlarge').off('click');
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
    $(window).on("autocharts.init", function(e){
        initBindings();
    });

});
