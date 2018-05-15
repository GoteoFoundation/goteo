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


d3.goteo.pieChart = function(settings){

// defaults
  var width = settings && settings.width || 450,
      height = settings && settings.height || 300,
      outerRadius = settings && settings.outerRadius || 150,
      innerRadius = settings && settings.innerRadius || 15;

  var _colors = d3.scaleOrdinal(d3.schemeCategory20c);
  var colors = function(d, i) {
    var label = d.label || d.data.label;
    if(label == 'Other' || label == 'Unknown') return '#BCBCBC';
    // console.log(label, d, i, _colors(i));
    return _colors(i);
  };

  function generator(selection){
    selection.each(function(dataSet) {
        var pie = d3.pie().value(function(d) { return d.value; });
        var tot = 0;
        dataSet.forEach(function(e){ tot += e.value; });
        //Create SVG element
        var viewBox = "0 0 " + width + " " + height;
        var svg = d3.select(this)
            .append("svg")
            .attr("viewBox", viewBox)
            .attr("preserveAspectRatio", "xMinYMin meet");

        var arc = d3.arc()
            .innerRadius(innerRadius)
            .outerRadius(outerRadius);

        var arcs = svg.selectAll("g.arc")
            .data(pie(dataSet))
            .enter()
            .append("g")
            .attr("class", "arc")
            .attr("transform", "translate(" + outerRadius + ", " + outerRadius + ")");

        arcs.append("path").attr("fill", colors)
                .attr("d", arc)
            .on("mouseover",function(e){
                d3.select(this)
                .attr("fill-opacity", ".8")
                .style("stroke", "white")
                .style("stroke-width", "1px");
            })
            .on("mouseout",function(e){
                d3.select(this)
                .attr("fill-opacity", "1")
                .style("stroke-width", "0px");
            })
            .transition()
            .duration(1000)
            .attrTween('d', function(d) {
                var interpolate = d3.interpolate({startAngle: 0, endAngle: 0}, d);
                return function(t) {
                    return arc(interpolate(t));
                };
            });
        arcs.append("svg:title")
                .text(function(d) { return d.data.label; });
        arcs.append("svg:text")
            .attr("transform", function(d){
                // d.innerRadius = outerRadius/2;
                // d.outerRadius = outerRadius;
                var c = arc.centroid(d);
                return "translate(" + c[0]*1.5 +"," + c[1]*1.5 + ")";
                // return "translate(" + arc.centroid(d) + ")";
              })
            .attr("text-anchor", "middle").text( function(d, i) {
                return (d.data.value / tot ) * 100 > 1 ? ((d.data.value / tot ) * 100).toFixed(1) + "%" : "";
                })
            .attr("fill","#fff")
            .classed("slice-label",true);
        // add legends with label + percent

        var legend = svg.append("g")
              .attr("font-family", "sans-serif")
              .attr("font-size", 10)
              .attr("text-anchor", "end")
              .selectAll("g")
              .data(dataSet)
              .enter().append("g")
              .attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });

          legend.append("rect")
              .attr("x", width - 19)
              .attr("width", 19)
              .attr("height", 19)
              .attr("fill", colors);

          legend.append("text")
              .attr("x", width - 24)
              .attr("y", 9.5)
              .attr("dy", "0.32em")
              .text(function(d) { return (( d.value / tot ) * 100).toFixed(1) + "% " + d.label; });
      });
  }

  return generator;
};


