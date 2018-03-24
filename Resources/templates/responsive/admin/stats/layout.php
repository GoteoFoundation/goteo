<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-stats') ?></h2>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function(){
    $('#main').on('click', '.admin-content .btn-group button', function() {
        $(this).addClass('active').siblings().removeClass('active');
    });
});
// @license-end
</script>

<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/admin/stats-invests.js"></script>

<?php $this->append() ?>
