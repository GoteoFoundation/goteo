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
      color = settings && settings.color || 'black',
      flash_color = settings && settings['flash-color'] || 'orange',
      flash_time = settings && settings['flash-time'] || 10,
      delay = settings && settings.delay || 200
      ;

    var findValue = function(ob, value) {
        var parts = value.split( '.' );
        var cur = ob;
        parts.forEach(function(item){
            if(cur.hasOwnProperty(item)) cur = cur[item];
            else return null;
            // console.log(item, 'curr', cur);
        });
        return cur != ob ? cur : null;
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
            console.log( 'discret values graph dataset',dataSet);
            var self = this;
            var children = d3.select(this).selectAll( '[data-property]' );
            children.each(function(it, i) {
                var child = d3.select(this);
                var property = child.attr( 'data-property' );
                var title = child.attr( 'data-title' );
                var tooltip = child.attr( 'data-tooltip' );
                var value = findValue(dataSet, property);
                // console.log('title', title);
                title = findValue(dataSet, title) || title;

                if(value !== null) {
                    value = value.toString();
                    // console.log('index', i, 'title', title, 'property', property, 'value', value);
                    // remove if existing and has different value, otherwise do nothing
                    var flash = false;
                    var svg = d3.select(this).select( 'svg' );
                    // console.log(svg, svg.empty());
                    if(!svg.empty()) {
                        // console.log(svg.select( 'g' ).select( 'text' ), svg.select( 'g' ).select( 'text' ).text());
                        if(svg.select( 'g' ).select( 'text' ).text() == value) return;
                        svg.remove();
                        flash = true;
                    }

                    svg = d3.select(this).append( 'svg' )
                        .attr( 'viewBox', '0 0 ' + width + ' ' + height)
                        .attr( 'preserveAspectRatio', 'xMinYMin meet');

                    var g = svg.append( 'g' );
                    var l = Math.min(1, Math.round(80 / value.length) / 10);
                    var c = color;
                    if(value.indexOf( '%' ) !== -1) {
                        c = value.indexOf( '-' ) === -1 ? 'green' : '#b00';
                    } else if(value.indexOf( '-' ) !== -1) {
                        c = '#b00';
                    }
                    var t = g.append( 'text' )
                        .text(value)
                        .attr( 'text-anchor', 'middle' )
                        .attr( 'font-size', l + 'em' )
                        .attr( 'x',  width/2 )
                        .attr( 'y', height/3 )
                        .attr( 'fill', flash ? flash_color : c )
                        .style( 'opacity', 0);
                    t.transition()
                        .delay( delay * i )
                        .style( 'opacity', 1)
                        .transition()
                        .duration( flash_time * 1000 )
                        .attr( 'fill', c )
                        ;
                        // .tween( 'text', tweenText( value ) );
                    if(tooltip) {
                        // console.log( 'tooltip', tooltip );
                        var div = d3.select( 'body').append( 'div' )
                            .attr( 'class', 'tooltip top' )
                            .style( 'opacity', 0) ;
                        div.append( 'div' )
                            .attr( 'class', 'tooltip-arrow' );

                        var v = findValue(dataSet, tooltip).toString() || tooltip;
                        var inner = div.append( 'div' )
                            .attr( 'class', 'tooltip-inner' )
                            .html( v );

                        svg.on( 'mouseover', function(d) {
                                var matrix = this.getScreenCTM()
                                    .translate(+ this.getAttribute("cx"), + this.getAttribute("cy"));

                                div.attr("transform", "translate(" + (matrix.e) + "," + (matrix.f - (height/3)) + ")")
                                    .style("left", (window.pageXOffset + matrix.e + (width/2) ) + "px")
                                    .style("top", (window.pageYOffset + matrix.f ) + "px");

                                div.transition()
                                    .duration( 200 )
                                    .style( 'opacity', .9 )
                                    .style("left", (window.pageXOffset + matrix.e + (width/2)) + "px")
                                    .style("top", (window.pageYOffset + matrix.f - (height/3)) + "px");
                                    ;
                            }).on( 'mouseout', function(d) {
                                div.transition()
                                    .duration( 500 )
                                    .style( 'opacity', 0 );
                            });
                    }
                    var w = width;
                    var h = height / 2;
                    g.append( 'foreignObject' )
                        .attr( 'x', 0)
                        .attr( 'y', height - h)
                        .attr( 'width', w)
                        .attr( 'height', h)
                        .append( 'xhtml:div' )
                        .style( 'width', w + 'px' )
                        .style( 'height', h + 'px' )
                        .style( 'font-size', '8px' )
                        .style( 'line-height', '1' )
                        .style( 'text-align', 'center' )
                        // .style( 'background-color', 'transparent' )
                        // .style({width: w + 'px',
                        //     height: h + 'px',
                        //     'font-size': '20px',
                        //     'background-color': 'white'
                        // })
                        .html(title)
                        ;
                }
            });
        });
    }

    return generator;
};
