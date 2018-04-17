<?php

$title= $this->section ? $this->text($this->blog_sections[$this->section]) : $this->tag->name;

$this->layout('blog/layout', [
	'bodyClass' => 'blog',
    'title' => $title ? ucfirst($title).' :: Goteo.org' : 'Blog',
    'meta_description' => ''
    ]);

$this->section('blog-content');

?>

<?= $this->insert('partials/components/main_slider', [
            	  'banners' => $this->slider_posts,
            	  'button_text' => $this->text('regular-read_more')
]) ?>
<?= $this->insert('blog/partials/list_posts') ?>

<?php if(!$this->tag): ?>
	<?= $this->insert('blog/partials/list_sticky') ?>
<?php endif; ?>

<?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>



<?php $this->replace() ?>