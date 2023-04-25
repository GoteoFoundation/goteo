<?php

$this->layout('admin/pages/layout');

$this->section('admin-container-body') ?>

  <?= $this->form_form($this->raw('form')) ?>

  </div>
</div>

<?php
    $this->replace();
?>
<?php $this->section('footer'); ?>
    <script type="text/javascript" src="<?= $this->asset('js/admin/page.js') ?>"></script>
<?php $this->append() ?>
