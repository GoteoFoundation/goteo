<?php
$this->layout('channel/call/layout', [
	'bodyClass' => 'channel-call',
    'title' => ucfirst($this->faq->name).' :: FAQ',
    'meta_description' => $this->faq->banner_description
    ]);

$this->section('channel-content');

?>
<?= $this->insert('channel/call/partials/faq/banner') ?>

<?= $this->insert('channel/call/partials/faq/questions') ?>

<?= $this->insert('channel/call/partials/faq/download') ?>

<?= $this->insert('channel/call/partials/sponsors') ?>

<?php $this->replace() ?>
