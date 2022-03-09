<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->channel->name ?></h2>

    <select id="channels" name="channel-list" class="form-control" onchange="location.href=this.value">
        <?php foreach ($this->channels as $channelId => $channelName) : ?>
            <option value="/admin/channelprojects/<?= $channelId ?>" <?= ($this->channel->id == $channelId)? 'selected="selected"' : "" ?> >
                <?= $channelName ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?= $this->form_form($this->raw('form')) ?>

<?php $this->replace() ?>
