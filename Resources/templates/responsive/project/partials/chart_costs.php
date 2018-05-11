<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

function printCosts() {

    var container =  "div.chart-costs";
    if(!$(container).length) return;

    // Clear if defined
    $(container).contents().remove();

    var width = 600,
    height = 600,
    radius = Math.min(width, height) / 2;

    var x = d3.scaleLinear()
        .range([0, 2 * Math.PI]);

    var y = d3.scaleLinear()
        .range([0, radius]);

    var partition = d3.partition();

    var arc = d3.arc()
        .startAngle(function(d) { return Math.max(0, Math.min(2 * Math.PI, x(d.x0))); })
        .endAngle(function(d) { return Math.max(0, Math.min(2 * Math.PI, x(d.x1))); })
        .innerRadius(function(d) { return Math.max(0, y(d.y0)); })
        .outerRadius(function(d) { return Math.max(0, y(d.y1)); });


    var svg = d3.select(container).append("svg")
        // .attr("width", width) // no widh/height definition so it's responsive
        // .attr("height", height)
        .attr("preserveAspectRatio", "xMidYMid meet")
        .attr("viewBox", "0 0 " + width + ' ' + height)
        .append("g")
        .attr("transform", "translate(" + width / 2 + "," + (height / 2) + ")");

    d3.select(self.frameElement).style("height", height + "px");

    d3.json("/api/projects/<?= $this->project->id ?>/charts/costs", function(error, root) {
      if (error) throw error;

      d3.select("g").append("svg:image")
        .attr('x', -78)
        .attr('y', -78)
        .attr('width', 156)
        .attr('height', 156)
        .attr("xlink:href", "")
        .on("mouseover", function() {
          var sel = d3.select(this);
          sel.moveToBack();
        });

      root = d3.hierarchy(root);
      // only sum final nodes so it will be a full circle
      root.sum(function(d) { return d.children ? 0 : d.size; });

      var g = svg.selectAll("g")
        .data(partition(root).descendants())
        .enter().append('g');

      var path = g.append("path")
        .attr("d", arc)
        .style("fill", function(d) { return d.data.color; })
        .on("click", click)
        .on("mouseover", doHover)
        .on("mouseout", unDoHover);
        ;

      var title = g.append("title")
        .text(function(d) { return d.data.title ? d.data.name + "\n" + d.data.title : d.data.name; });

      var text = g.append("text")
        .attr("transform", function(d) {
          return "rotate(" + computeTextRotation(d) + ")";
        })
        .attr("x", function(d) {
          return y(d.y0);
        })
        .attr("dx", "6") // margin
        .attr("dy", ".35em") // vertical-align
        .text(function(d) { return d.data.title ? d.data.title : d.data.name; });

      // hide root
      d3.select("g g").transition().duration(200).attr("opacity", "0");

      function click(d) {

        // fade out all text elements
        text.transition().attr("opacity", 0);

        svg.transition()
          .duration(750)
          .tween("scale", function() {
            var xd = d3.interpolate(x.domain(), [d.x0, d.x1]),
                yd = d3.interpolate(y.domain(), [d.y0, 1]),
                yr = d3.interpolate(y.range(), [d.y0 ? 20 : 0, radius]);
            return function(t) { x.domain(xd(t)); y.domain(yd(t)).range(yr(t)); };
          })
          .selectAll("path")
          .attrTween("d", function(d) { return function() { return arc(d); }; })
          .on("end", function(e, i) {
            // check if the animated element's data e lies within the visible angle span given in d
            if (e.x0 >= d.x0 && e.x0 < d.x1) {
              // get a selection of the associated text element
              var arcText = d3.select(this.parentNode).select("text");
              // fade in the text element and recalculate positions
              arcText.transition().duration(750)
                .attr("opacity", 1)
                .attr("transform", function() {
                  return "rotate(" + computeTextRotation(e) + ")"
                })
                .attr("x", function(d) {
                  return y(d.y0);
                });
            }
          });
      }

      function doHover(d) {
        d3.select(this.parentNode.childNodes[0]).transition().duration(200).attr("opacity", "0.1");
        d3.select("g g").transition().duration(200).attr("opacity", "0");
      }

      function unDoHover(d) {
        d3.select(this.parentNode.childNodes[0]).transition().duration(200).attr("opacity", "1");
        d3.select("g g").transition().duration(200).attr("opacity", "0");
      }

      function computeTextRotation(d) {
        // console.log('computeTextRotation', d, x);
        return (x(d.x0 + (d.x1 - d.x0 )/ 2) - Math.PI / 2) / Math.PI * 180;
      }
      d3.selection.prototype.moveToBack = function() {
        return this.each(function() {
          var firstChild = this.parentNode.firstChild;
          if (firstChild) {
            this.parentNode.insertBefore(this, firstChild);
          }
        });
      };
    });
}

$(function(){
    printCosts();
    $(window).on("pronto.render", function() {
        printCosts();
    });
});

// @license-end
</script>
