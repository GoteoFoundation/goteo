<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$categories  = $vars['categories'];
?>
<div class="side_widget">
    <div class="block categories rounded-corners">
        <p class="title"><?php echo Text::get('node-side-searcher-bycategory') ?></p>
        <ul>
            <?php foreach ($categories as $cat=>$catData) : ?>
            <li><a href="<?php echo 'category-' . $cat ?>" class="show_cat"><?php echo $catData['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>
