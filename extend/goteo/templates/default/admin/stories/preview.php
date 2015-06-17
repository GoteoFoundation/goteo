<?php $this->layout('admin/layout') ?>


<?php $this->section('admin-content') ?>

<div class="widget stories-home" style="padding:0;">

    <div class="stories-banners-container rounded-corners-bottom" style="position:relative;">

    <?= \Goteo\Core\View::get('stories/story.html.php', array('story' => $this->raw('story'))) ?>

    </div>

</div>

<?php $this->replace() ?>
