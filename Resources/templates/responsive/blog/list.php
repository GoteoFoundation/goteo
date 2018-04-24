<?php

$this->layout('blog/layout', [
	'bodyClass' => 'blog',
    'title' => 'Blog',
    'meta_description' => ''
    ]);

$this->section('blog-content');

?>

<?= $this->insert('partials/components/main_slider', [
            	  'banners' => $this->slider_posts,
            	  'button_text' => $this->text('regular-read_more')
]) ?>
<?= $this->insert('blog/partials/list_posts') ?>
<?= $this->insert('blog/partials/list_sticky') ?>


<?php $this->replace() ?>