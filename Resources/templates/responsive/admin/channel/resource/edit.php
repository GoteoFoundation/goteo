<?php

$this->layout('admin/channel/resource/layout');

$this->section('admin-search-box-addons');
?>
<a class="btn btn-cyan" href="/admin/channelresource?<?= $this->get_querystring() ?>"><i class="fa fa-arrow-circle-left"></i> <?= $this->text('admin-back-list') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

    <h4 class="title">
    	<?= $this->post->id ? $this->text('admin-channel-resource-add', "#{$this->post->id}") : $this->text('admin-channel-resource-add') ?>
    	
    </h4>


    <?= $this->form_form($this->raw('form')) ?>

  </div>
</div>

<?php $this->replace() ?>
