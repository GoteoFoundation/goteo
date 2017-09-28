<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-project') ?>

  <?= $this->form_start($this->raw('form')) ?>

  <?php foreach($this->costs as $cost): ?>
      <div class="panel section-content" data-id="<?= $cost->id ?>">
        <div class="panel-body">
          <div class="pull-left" style="width: 85%;">
            <?= $this->insert('dashboard/project/translate/partials/cost_form', [
              'form' => $this->raw('form'),
              'cost' => $cost,
              'lang' => $this->lang
              ]) ?>
          </div>
          <div class="pull-right text-right" style="width: 15%;">
            <h4 title="<?= $this->types[$cost->type] ?>">
              <?= amount_format($cost->amount) ?><br>
              <img src="<?= $this->asset('/img/project/needs/'.$cost->type.'.png') ?> ">
            </h4>
          </div>
        </div>
      </div>
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
