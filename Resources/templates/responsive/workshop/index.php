<?php

$meta_img = $this->workshop->header_image ? $this->workshop->getHeaderImage()->getLink(700, 700, false, true) : $this->asset('img/blog/header_default.png') ;

$this->layout('workshop/layout', [
	'bodyClass' => 'workshop',
    'title' => $this->workshop->title,
    'meta_description' => $this->workshop->subtitle,
    'tw_image' => $meta_img
    ]);

$this->section('workshop-content');

?>

<?= $this->insert('workshop/partials/banner_header') ?>

<?= $this->insert('workshop/partials/main_call_to_action') ?>

<?= $this->insert('workshop/partials/intro_text') ?>

<?= $this->footer_sponsors ? $this->insert('workshop/partials/partner_footer', ['footer_sponsors' => $this->footer_sponsors]) : '' ?>

<?= $this->insert('workshop/partials/main_info') ?>

<?= !$this->workshop->online ? $this->insert('workshop/partials/how_to_get') : '' ?>

<?= !$this->workshop->expired() ? $this->insert('workshop/partials/extra_call_to_action') : '' ?>

<?= $this->related_workshops ? $this->insert('workshop/partials/related_workshops') : '' ?>

<?= $this->insert('workshop/partials/stories') ?>

<?= $this->insert('workshop/partials/posts') ?>

<?= $this->workshop->event_type=='fundlab-esil' ? $this->insert('workshop/partials/partner') : '' ?>

<?= $this->workshop->event_type=='crowdcoop' ? $this->insert('workshop/partials/partner_singulars') : '' ?>

<?php $this->replace() ?>