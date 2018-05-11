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

d3.goteo = d3.goteo || {};


// generate a graph based on
// https://github.com/mozilla/metrics-graphics/wiki/List-of-Options#graphic
//
d3.goteo.timemetricsChart = function(settings){
  // defaults
  var width = settings && settings.width || 600,
      height = settings && settings.height || 300,
      title = settings && settings.title || 'Time series',
      description = settings && settings.description || 'Show events in a timeline',
      min_date = settings && settings.min_date,
      max_date = settings && settings.max_date,
      format = settings && settings.format || '%VALUE%',
      field = settings && settings.field || 'value'
      ;
      console.log('settings', settings);

  function generator(selection){
    selection.each(function(dataSet) {
        console.log('dataset',dataSet);
        var self = this;
        MG.data_graphic({
            title: title,
            description: description,
            data: dataSet,
            y_accessor: field,
            width: width,
            height: height,
            interpolate: d3.curveLinear,
            // european_clock: true,
            // missing_is_zero: true,
            min_x: min_date,
            max_x: max_date,
            target: self,
            linked: true,
            mouseover: function(d, i) {
                var df = d3.timeFormat('%b %d, %Y');
                var date = df(d.date);
                var y_val = format.replace("%VALUE%", d[field])
                                  .replace("%TOTAL%", d.count);
                // console.log(d, format, y_val);

                d3.select(self).select('svg .mg-active-datapoint')
                    .text(date +  '   ' + y_val);
            }
        });

        // Transform the svg to a responsive one
        d3.select(self).select('svg').attr('width', null);
        d3.select(self).select('svg').attr('height', null);
        d3.select(self).select('svg').attr('viewBox', '0 0 ' + width + ' ' + height);
        d3.select(self).select('svg').attr("preserveAspectRatio", "xMinYMin meet");
      });
  }

  return generator;
};


