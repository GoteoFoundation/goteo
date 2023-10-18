<?php

$image = $this->channel->logo ? $this->channel->logo->getLink(700, 700, false, true) : $this->asset('img/blog/header_default.png') ;

$this->layout('channel/call/layout', [
    'bodyClass' => 'blog channel-call',
    'title' => $this->post->title,
    'meta_description' => $this->post->subtitle ?: $this->post->title,
    'tw_image' => $image,
    'og_image' => $image
]);
?>

<?php
    $this->section('header');
?>

<header class="section banner-header">
    <?= $this->insert('channel/call/partials/navbar') ?>
</header>

<?php
    $this->append();
?>

<?php
$this->section('channel-content');
?>

<?= $this->insert('blog/partials/blog_header') ?>
<?= $this->insert('blog/partials/content') ?>
<?= $this->insert('blog/partials/tags') ?>
<?= $this->insert('blog/partials/related_posts') ?>

<?php $this->replace() ?>
