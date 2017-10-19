<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1>
        <?= $this->text('dashboard-translate-profile') ?>
        <?= $this->insert('dashboard/partials/translate_menu', ['class' => 'pull-right', 'base_link' => '/dashboard/settings/profile/', 'exit_link' => '/dashboard/settings/profile', 'lang' => $this->current]) ?>
    </h1>

    <p><?= $this->text('dashboard-translate-profile-desc', ['%LANG%' => '<strong><em>' . $this->languages[$this->current] . '</em></strong>', '%URL%' => '/dashboard/settings/profile']) ?></p>

    <?= $this->supply('dashboard-content-form', function() {return $this->form_form($this->raw('form'));}) ?>


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
