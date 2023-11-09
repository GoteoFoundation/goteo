<?php
$this->layout('channel/call/layout', [
	'bodyClass' => 'channel-call',
    'title' => ucfirst($this->faq->name).' :: FAQ',
    'meta_description' => $this->faq->banner_description
    ]);

$this->section('channel-content');
?>

<?= $this->insert('channel/call/partials/banner_header') ?>

<?= $this->insert('channel/call/partials/faq/questions_list') ?>

<?php $this->replace() ?>
