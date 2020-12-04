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


d3.goteo.arcChart = function(settings){

    // defaults
    var w = settings && settings.width || 200,
      h = settings && settings.height || 200,
      outerRadiusArc = settings && settings.outerRadius || w/2,
      innerRadiusArc = settings && settings.innerRadius || 70,
      shadowWidth = settings && settings.shadowWidth || 5,
      type = settings && settings.type || "amount";

    // var w=200,h=200;

    // var outerRadiusArc=w/2;
    // var innerRadiusArc=70;
    // var shadowWidth=5;

    var outerRadiusArcShadow=innerRadiusArc+1;
    var innerRadiusArcShadow=innerRadiusArc-shadowWidth;
  

    var color = d3.scaleOrdinal()
        .range(['#3392AA', '#AC438E']);


//   var _colors = d3.scaleOrdinal(d3.schemeCategory20c);
//   var colors = function(d, i) {
//     var label = d.label || d.data.label;
//     if(label == 'Other' || label == 'Unknown') return '#BCBCBC';
//     // console.log(label, d, i, _colors(i));
//     return _colors(i);
//   };

  function generator(selection){
    selection.each(function(dataSet) {
        var svg = d3.select(this)
        .append("svg");
    
        svg.attr('width', w)
            .attr('height', h)
            .attr('class', 'shadow');
        svg.append('g')
            .attr('transform', 'translate('+w/2+','+h/2+')');
    
        var pie = d3.pie().value(function(d) { return d.value; });

        var total=0;

        dataSet.forEach(function(d){
            total+= d.value;
        });
    
    
        var drawChart=function(svg,outerRadius,innerRadius,fillFunction,className){
    
            var arc = d3.arc()
                .innerRadius(outerRadius)
                .outerRadius(innerRadius);
    
            svg.select('g').selectAll('.'+className)
                .data(pie(dataSet))
                .enter()
                .append('path')
                .attr('class', className)
                .attr('d', arc)
                .attr('fill', fillFunction)
                .transition()
                .duration(1000)
                .attrTween('d', function(d) {
                    var interpolate = d3.interpolate({startAngle: 0, endAngle: 0}, d);
                    return function(t) {
                        return arc(interpolate(t));
                    };
                });
        };
    
        var addText= function (text,y,size) {
            svg.select('g').append('text')
                .text(text)
                .attr('text-anchor', 'middle')
                .attr('y', y)
                .style('fill', '#3D3D3D')
                .style('font-size', size)
                .style('font-weight', 'bold');
        };
    
        var restOfTheData = function(){
    
            if (type == "amount") {
                var rest=dataSet[1].value;

                addText(rest, 10, '30px', '#3D3D3D', 'bold');
        
                addText('EUROS',25,'10px', '#FFF', 'normal');
        
            } else if (type == "percentage") {
                var percentage = 0;

                if (total) {
                    percentage = (dataSet[0].value/total)*100;
                } 
        
                percentage=percentage.toFixed();
        
                addText(percentage+'%', 10, '30px');
            }
        };
    
        drawChart(svg,outerRadiusArc,innerRadiusArc,function(d,i){
            return color(d.data.label);
        },'path1');
    
        drawChart(svg,outerRadiusArcShadow,innerRadiusArcShadow,function(d,i){
            var c=d3.hsl(color(d.data.label));
            return d3.hsl((c.h+5), (c.s -.07), (c.l -.15));
        },'path2');
    
        restOfTheData();
    
      });
  }

  return generator;
};


