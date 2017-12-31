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
<script type="text/javascript" src="<?php echo SRC_URL ?>/assets/js/charts/responsive_pie.js"></script>
<script type="text/javascript">

$(function(){
    $('.d3-chart.percent-pie').each(function(){
        var self = $(this)[0];
        console.log('source', $(this).data('source'));
        d3.json($(this).data('source'), function (error, data) {
            if(error) throw error;
            console.log('data', data);
            var pieData = data.map(function(x){
                return {label:x.label, value: x.counter};
            });
            var pie = d3.goteo.piechart();
            d3.select(self).datum(pieData).call(pie);

            // Filling the Legend
            // data.forEach(function(item, i){
            //     var button = "<a href='#' class='button tiny' style='background-color:" + colors(i) + ";'>&nbsp</a>";
            //     $("#pieLegend").append("<li>" + button + " " + item.value + " years (" + item.percentage + "%)</li>");
            // });

        });
    });
});

</script>
<?php $this->append() ?>
