<?php $this->layout('admin/layout') ?>

<?php

$this->section('admin-content');

$sender = $this->sender;
$status = $sender->getStatusObject();

?>

<a href="/admin/mailing" class="button"><?= $this->text('admin-mailing-back-to-list') ?></a>

<a href="<?= $this->link ?>" class="button" target="_blank"><?= $this->text('admin-mailing-view-message') ?></a>

<div class="widget">
    <p><?= $this->text('admin-mailing-communicating-with', ['%DESC%' => $this->raw('filters_txt')]) ?> </p>
    <p><?= $this->text('admin-mailing-new-massive', ['%TOTAL%' => '<strong>' . $this->total . '</strong>']) ?></p>

    <p>
       <?= $this->text('admin-mailing-subject') ?>: <strong><?= $sender->getMail()->subject ?></strong><br />
       <?= $this->text('admin-mailing-created') ?>: <strong><?= $sender->date ?></strong><br />
       <?= $this->text('admin-mailing-status') ?>: <span class="label label-<?= $sender->getStatus() ?>"><?= $sender->getStatus() ?></span>
    </p>

    <?php if($this->is_module_admin('Newsletter')): ?>
        <p><a href="/admin/newsletter/detail/<?= $sender->id ?>" class="button"><?= $sender->getStatus() == 'inactive' ? $this->text('admin-mailing-view-and-activate') : $this->text('admin-mailing-view-in-newsletter') ?></a></p>
    <?php endif ?>

    <?php if($this->is_module_admin('Sent')): ?>
        <p><a href="/admin/sent/detail/<?= $this->mail ?>"><?= $this->text('admin-mailing-view-in-sent') ?></a></p>
    <?php endif ?>

</div>

<?php $this->replace() ?>
