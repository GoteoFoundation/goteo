<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$stories = $this['stories'];
// random y que solo pinte seis si hubiera más
if (count($stories) > 6) {
	shuffle($stories);
	$stories = array_slice($stories, 0, 6);
}
?>
<div class="widget projects">

    <h2 class="title"><?php echo Text::get('home-stories-header'); ?></h2>

    <?php foreach ($stories as $story) : ?>
            <?php echo $story->title ?>
            <?php echo \trace($story); ?>
    <?php endforeach ?>

</div>
