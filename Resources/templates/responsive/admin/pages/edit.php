<?php

$this->layout('admin/pages/layout');

$this->section('admin-container-body') ?>

  <?= $this->form_form($this->raw('form')) ?>

  </div>
</div>

<?php $this->replace() ?>
