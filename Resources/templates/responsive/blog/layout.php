<?php

$this->layout('layout', [
    'bodyClass' => 'blog',
    'title' =>  $this->post->title,
    'meta_description' => $this->text_truncate($this->post->text, 155),
    'tw_image' =>  $this->post->image ? $this->post->image->getlink(400,0, false, true) : ''
    ]);



$this->section('head');

?>

<link rel="stylesheet" href="/assets/vendor/selection-sharer/selection-sharer.css" />

<?php

$this->append();



$this->section('header-navbar-brand');

?>
    <a class="navbar-brand" href="<?= $this->get_config('url.main') ?>"><img src="<?= $this->asset('/img/icons/foundation.svg') ?>" class="logo" alt="Goteo"></a>
    <h3><?= $this->text('home-foundation-title') ?></h3>

<?php

$this->replace();

$this->section('content');

?>

<div class="blog">

    <?= $this->supply('blog-content') ?>

</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<link rel="stylesheet" href="/assets/vendor/selection-sharer/selection-sharer.js" />

<script>
	$('div').selectionSharer();
</script>

<?php $this->append() ?>

