<?php

//$meta_img = $this->workshop->header_image ? $this->workshop->getHeaderImage()->getLink(700, 700, false, true) : $this->asset('img/blog/header_default.png') ;

$this->layout('channel/call/layout', [
	'bodyClass' => 'channel-call',
    'title' => ucfirst($this->faq->name).' :: FAQ',
    'meta_description' => $this->faq->banner_description,
    'tw_image' => $meta_img
    ]);

$this->section('channel-content');

?>

<?= $this->insert('channel/call/partials/faq/banner') ?>

<?= $this->insert('channel/call/partials/faq/questions') ?>

<?= $this->insert('channel/call/partials/faq/download') ?>

<?php $this->replace() ?>