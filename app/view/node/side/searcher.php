<?php
use Goteo\Library\Text,
    Goteo\Core\View;

?>
<div class="side_widget">
    <?php foreach ($vars['searcher'] as $cat => $label) : ?>
    <div class="line button rounded-corners<?php if ($cat == 'promote' && !$vars['hide_promotes']) echo ' current' ?>">
        <p><a href="<?php echo $cat ?>" class="show_cat"><?php echo $label ?></a></p>
    </div>
    <?php endforeach; ?>

</div>
