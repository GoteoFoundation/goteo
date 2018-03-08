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


d3.goteo.discretevaluesChart = function(settings){
    // defaults
    var width = settings && settings.width || 64,
      height = settings && settings.height || 64,
      title = settings && settings.title || 'Time series',
      description = settings && settings.description || 'Show events in a timeline';

    var findValue = function(ob, value) {
        var parts = value.split('.');
        var cur = ob;
        parts.forEach(function(item){
            if(cur.hasOwnProperty(item)) cur = cur[item];
            else return null;
            // console.log(item, 'curr', cur);
        });
        return cur;
    };

    /**
     * Tween functions
     */
    var tweenText = function( newValue ) {
        return function() {
          // get current value as starting point for tween animation
          var currentValue = +this.textContent;
          // create interpolator and do not show nasty floating numbers
          var i = d3.interpolateRound( currentValue, newValue );

          return function(t) {
            this.textContent = i(t);
          };
        }
    };

    function generator(selection){
        selection.each(function(dataSet) {
            console.log('discret values graph dataset',dataSet);
            var self = this;
            var children = d3.select(this).selectAll('[data-property]');
            children.each(function(it, i) {
                var child = d3.select(this);
                var title = child.attr('data-title');
                var property = child.attr('data-property');
                var value = findValue(dataSet, property);
                if(value !== null) {
                    // console.log(it, i, title, property, value);
                    // remove if existing and has different value, otherwise do nothing
                    var svg = d3.select(this).select('svg');
                    console.log(svg, svg.empty());
                    if(!svg.empty()) {
                        console.log(svg.select('g').select('text'), svg.select('g').select('text').text());
                        if(svg.select('g').select('text').text() == value) return;
                        svg.remove();
                    }
                    svg = d3.select(this).append("svg")
                        .attr("viewBox", "0 0 " + width + " " + height)
                        .attr("preserveAspectRatio", "xMinYMin meet");

                    var g = svg.append('g');
                    g.append('text')
                        .text(value)
                        .attr( 'text-anchor', 'middle' )
                        .attr( 'x',  width/2 )
                        .attr( 'y', height/3 )
                        .attr( 'fill', 'red')
                        .style('opacity', 0)
                        .transition()
                        .delay( 300 * i )
                        .attr( 'fill', 'black')
                        .style('opacity', 1);
                        // .tween( 'text', tweenText( value ) );

                    var w = width;
                    var h = height / 2;
                    g.append("foreignObject")
                        .attr('x', 0)
                        .attr('y', height - h)
                        .attr('width', w)
                        .attr('height', h)
                        .append("xhtml:div")
                        .style('width', w + 'px')
                        .style('height', h + 'px')
                        .style('font-size', '8px')
                        .style('line-height', '1')
                        .style('text-align', 'center')
                        .style('background-color', 'transparent')
                        // .style({width: w + 'px',
                        //     height: h + 'px',
                        //     "font-size": "20px",
                        //     "background-color": "white"
                        // })
                        .html(title)
                        ;
                }
            });
        });
    }

    return generator;
};
