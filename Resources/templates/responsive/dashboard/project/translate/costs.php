<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-project') ?>

  <?= $this->form_start($this->raw('form')) ?>

  <?php foreach($this->costs as $cost): ?>
     <?= $this->insert('dashboard/project/translate/partials/cost_item', ['cost' => $cost, 'form' => $this->raw('form')]) ?>
  <?php endforeach ?>

  <?= $this->form_end($this->raw('form')) ?>

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
