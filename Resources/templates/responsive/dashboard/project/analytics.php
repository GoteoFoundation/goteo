<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->text('dashboard-menu-projects-analytics') ?></h1>

    <?= $this->supply('dashboard-content-analytics', $this->insert('dashboard/project/partials/analytics')) ?>

    <?= $this->supply('dashboard-content-form', function() {return $this->form_form($this->raw('form'));}) ?>

  </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>
<script type="text/javascript">

$(function(){
    var makePie = function(selector, data) {
        var svg = d3.select(selector),
        width = +svg.attr("width"),
        height = +svg.attr("height"),
        radius = Math.min(width, height) / 2,
        g = svg.append("g").attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

        var color = d3.scaleOrdinal(["#98abc5", "#8a89a6", "#7b6888", "#6b486b", "#a05d56", "#d0743c", "#ff8c00"]);

        var pie = d3.pie()
            .sort(null)
            .value(function(d) { return d.population; });

        var path = d3.arc()
            .outerRadius(radius - 10)
            .innerRadius(0);

        var label = d3.arc()
            .outerRadius(radius - 40)
            .innerRadius(radius - 40);


      var arc = g.selectAll(".arc")
        .data(pie(data))
        .enter().append("g")
          .attr("class", "arc");

      arc.append("path")
          .attr("d", path)
          .attr("fill", function(d) { return color(d.data.counter); });

      arc.append("text")
          .attr("transform", function(d) { return "translate(" + label.centroid(d) + ")"; })
          .attr("dy", "0.35em")
          .text(function(d) { return d.data.counter; });
    };

    // var parseDate  = d3.time.format('%Y-%m-%d').parse;
    d3.json('/api/charts/<?= $this->project->id ?>/referer/project', function (error, data) {
        if (error) throw error;
        makePie('svg.d3-chart.percent-pie', data);
    });
});

</script>
<?php $this->append() ?>
