<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$icons  = $vars['icons'];
?>
<div class="side_widget">
    <?php foreach ($vars['searcher'] as $cat => $label) :
        if ($cat == 'byreward') : ?>
    <div class="block rewards rounded-corners">
        <p class="title"><?php echo $label ?></p>
        <p class="items">
            <?php foreach ($vars['discover']['byreward'] as $icon=>$projs) : ?>
        	<a href="<?php echo $cat . '-' . $icon ?>" class="show_cat tipsy <?php echo $icon ?>" title="<?php echo $icons[$icon]->name ?>"><?php echo $icons['file']->name ?></a>
            <?php endforeach; ?>
    </div>
        <?php else:  ?>
    <div class="line button rounded-corners<?php if ($cat == 'promote' && !$vars['hide_promotes']) echo ' current' ?>">
        <p><a href="<?php echo $cat ?>" class="show_cat"><?php echo $label ?></a></p>
    </div>
        <?php endif; ?>
    <?php endforeach; ?>

</div>
