<?php
$this->layout('channel/call/layout', [
	'bodyClass' => 'channel-call',
    'title' => ucfirst($this->faq->name).' :: FAQ',
    'meta_description' => $this->faq->banner_description
    ]);

$this->section('channel-content');

?>
<?= $this->insert('channel/call/faq/partials/banner') ?>

<?= $this->insert('channel/call/faq/partials/questions') ?>

<?= $this->insert('channel/call/faq/partials/download') ?>

<?= $this->insert('channel/call/partials/sponsors') ?>

<?php $this->replace() ?>
