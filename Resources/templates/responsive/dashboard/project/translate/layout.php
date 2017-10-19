<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->text('dashboard-translate-' . ($this->step ? $this->step : 'project')) ?></h1>

    <?= $this->supply('dashboard-translate-tabs', $this->insert('dashboard/project/translate/partials/tabs', ['zones' => $this->zones])) ?>

    <?= $this->supply('dashboard-translate-project') ?>

  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){
        $('.autoform .help-text').attr('data-desc', '<?= $this->ee($this->text('translator-original-text'), 'js') ?>: ');
    });
    // @license-end
</script>
<?php $this->append() ?>
