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
    $('.d3-chart.percent-pie').each(function(){
        var self = $(this)[0];
        $(this).css('cursor', 'pointer');
        // console.log('source', $(this).data('source'));
        d3.json($(this).data('source'), function (error, data) {
            if(error) throw error;
            // console.log('data', data);
            var pieData = data.map(function(x){
                return {label:x.label, value: x.counter};
            });
            var pie = d3.goteo.piechart();
            d3.select(self).datum(pieData).call(pie);
        });
    });
    $('.d3-chart').on('click', function(e) {
        e.preventDefault();
        var $wrap = $(this).closest('.chart-wrapper');
        $wrap.toggleClass('d3-chart-wide');
    });
});

</script>
<?php $this->append() ?>
