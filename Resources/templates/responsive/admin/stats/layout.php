<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-stats') ?></h2>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

    <?php $this->section('admin-stats-body') ?>
        <?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters')) ?>

        <?php $this->section('admin-stats-container') ?>
        <?php $this->stop() ?>
    <?php $this->stop() ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
$(function(){
    $('#main').on('click', '.admin-content .btn-group button', function() {
        $(this).addClass('active').siblings().removeClass('active');
    });
});
</script>

<?php $this->append() ?>
