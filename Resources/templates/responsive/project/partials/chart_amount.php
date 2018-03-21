<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

function printAmount() {

    var container =  "div.chart-amount";
    if(!$(container).length) return;

    // Clear if defined
    $(container).contents().remove();

    function addAxesAndLegend (svg, xAxis, yAxis, margin, chartWidth, chartHeight) {
      var legendWidth  = 200,
          legendHeight = 60;

      // clipping to make sure nothing appears behind legend
      svg.append('clipPath')
        .attr('id', 'axes-clip')
        .append('polygon')
          .attr('points', (-margin.left)                 + ',' + (-margin.top)                 + ' ' +
                          (chartWidth - legendWidth - 1) + ',' + (-margin.top)                 + ' ' +
                          (chartWidth - legendWidth - 1) + ',' + legendHeight                  + ' ' +
                          (chartWidth + margin.right)    + ',' + legendHeight                  + ' ' +
                          (chartWidth + margin.right)    + ',' + (chartHeight + margin.bottom) + ' ' +
                          (-margin.left)                 + ',' + (chartHeight + margin.bottom));

      var axes = svg.append('g')
        .attr('clip-path', 'url(#axes-clip)');

      axes.append('g')
        .attr('class', 'x axis')
        .attr('transform', 'translate(0,' + chartHeight + ')')
        .call(xAxis);

      axes.append('g')
        .attr('class', 'y axis')
        .call(yAxis)
        .append('text')
          .attr('transform', 'rotate(-90)')
          .attr('y', 6)
          .attr('dy', '.71em')
          .style('text-anchor', 'end')
          .text('Conseguido (€)');

      var legend = svg.append('g')
        .attr('class', 'legend')
        .attr('transform', 'translate(' + (chartWidth - legendWidth) + ', 0)');

      legend.append('rect')
        .attr('class', 'legend-bg')
        .attr('width',  legendWidth)
        .attr('height', legendHeight);

      legend.append('rect')
        .attr('class', 'outer')
        .attr('width',  75)
        .attr('height', 20)
        .attr('x', 10)
        .attr('y', 10);

      legend.append('text')
        .attr('x', 115)
        .attr('y', 25)
        .text('<?= $this->text("project-chart-amount-reached") ?>');

      legend.append('path')
        .attr('class', 'median-line')
        .attr('d', 'M10,47L85,48');

      legend.append('text')
        .attr('x', 115)
        .attr('y', 50)
        .text('<?= $this->text("project-chart-amount-ideal") ?>');

    }

    function drawPaths (svg, data, x, y) {
      var upperOuterArea = d3.area()
        .curve(d3.curveBasis)
        .x (function (d) { return x(d.date) || 1; })
        .y0(function (d) { return y(d.cumulative); })
        .y1(function (d) { return y(0); });

      var medianLine = d3.line()
        .curve(d3.curveBasis)
        .x(function (d) { return x(d.date); })
        .y(function (d) { return y(d.ideal); });

      svg.datum(data);

      svg.append('path')
        .attr('class', 'area upper outer')
        .attr('d', upperOuterArea)
        .attr('clip-path', 'url(#rect-clip)');

      svg.append('path')
        .attr('class', 'median-line')
        .attr('d', medianLine)
        .attr('clip-path', 'url(#rect-clip)');
    }

    function addMarker (marker, svg, chartHeight, x) {
      var radius = 32,
          xPos = x(marker.date) - radius - 3,
          yPosStart = chartHeight - radius - 3,
          yPosEnd = (marker.type === 'Client' ? 80 : 160) + radius - 3;

      var markerG = svg.append('g')
        .attr('class', 'marker '+marker.type.toLowerCase())
        .attr('transform', 'translate(' + xPos + ', ' + yPosStart + ')')
        .attr('opacity', 0);

      markerG.transition()
        .duration(1000)
        .attr('transform', 'translate(' + xPos + ', ' + yPosEnd + ')')
        .attr('opacity', 1);

      markerG.append('path')
        .attr('d', 'M' + radius + ',' + (chartHeight-yPosStart) + 'L' + radius + ',' + (chartHeight-yPosStart))
        .transition()
          .duration(1000)
          .attr('d', 'M' + radius + ',' + (chartHeight-yPosEnd) + 'L' + radius + ',' + (radius*2));

      markerG.append('circle')
        .attr('class', 'marker-bg')
        .attr('cx', radius)
        .attr('cy', radius)
        .attr('r', radius);

      markerG.append('text')
        .attr('x', radius)
        .attr('y', radius*0.9)
        .text(marker.type);

      markerG.append('text')
        .attr('x', radius)
        .attr('y', radius*1.5)
        .text(marker.version);
    }

    function startTransitions (svg, chartWidth, chartHeight, rectClip, markers, x) {
      rectClip.transition()
        .duration(1000*markers.length)
        .attr('width', chartWidth);

      markers.forEach(function (marker, i) {
        setTimeout(function () {
          addMarker(marker, svg, chartHeight, x);
        }, 1000 + 500*i);
      });
    }

    function makeChart (data, markers) {
      var svgWidth  = 600,
          svgHeight = 400,
          margin = { top: 20, right: 20, bottom: 40, left: 50 },
          chartWidth  = svgWidth  - margin.left - margin.right,
          chartHeight = svgHeight - margin.top  - margin.bottom;

      var x = d3.scaleTime().range([0, chartWidth])
                .domain(d3.extent(data, function (d) { return d.date; })),
          y = d3.scaleLinear().range([chartHeight, 0])
                .domain([0, d3.max(data, function (d) { return d.ideal*1.5; })]);

      var xAxis = d3.axisBottom(x)
                    .tickSizeInner(-chartHeight).tickSizeOuter(0).tickPadding(10),
          yAxis = d3.axisLeft(y)
                    .tickSizeInner(-chartWidth).tickSizeOuter(0).tickPadding(10);

      var svg = d3.select(container).append('svg')
        // .attr('width',  svgWidth)
        // .attr('height', svgHeight)
        .attr("preserveAspectRatio", "xMidYMid meet")
        .attr("viewBox", "0 0 " + svgWidth + ' ' + svgHeight)
        .append('g')
        .attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

      // clipping to start chart hidden and slide it in later
      var rectClip = svg.append('clipPath')
        .attr('id', 'rect-clip')
        .append('rect')
        .attr('width', 0)
        .attr('height', chartHeight);

      addAxesAndLegend(svg, xAxis, yAxis, margin, chartWidth, chartHeight);
      drawPaths(svg, data, x, y);
      startTransitions(svg, chartWidth, chartHeight, rectClip, markers, x);
    }

    var parseDate  = d3.timeParse('%Y-%m-%d');
    d3.json('/api/projects/<?= $this->project->id ?>/charts/invests', function (error, rawData) {
      if (error) throw error;

      var data = rawData.map(function (d) {
        return {
          date:  d.date ? parseDate(d.date) : '',
          cumulative: d.cumulative,
          ideal: d.ideal
        };
      });

      var markers = [{date:parseDate('<?= $this->project->willpass ?>'),type:"<?= $this->text('project-chart-amount-end-round') ?>" , version: "1"}];

      makeChart(data, markers);

    });
}

$(function(){
    printAmount();
    $(window).on("pronto.render", function() {
        printAmount();
    });
});
// @license-end
</script>
