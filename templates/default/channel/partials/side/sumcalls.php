<?php
use Goteo\Library\Text,
    Goteo\Core\View;

// ver página de ver mas convocatorias
?>
<div class="side_widget convocatorias activable">
    <div class="block rounded-corners">
        <p class="title"><?php echo Text::get('node-side-sumcalls-header'); ?></p>
        <div style="margin-bottom:6px">
        	<p class="text"><?php echo Text::get('node-side-sumcalls-budget'); ?></p>
    	    <p class="quantity all"><span><?php echo \amount_format($vars['sumcalls']['budget']) ?></span></p>
        </div>
        <div>
	        <p class="text"><?php echo Text::get('node-side-sumcalls-rest'); ?></p>
	        <p class="quantity rest"><span><?php echo \amount_format($vars['sumcalls']['rest']) ?></span></p>
        </div>
    </div>
    <div class="half calls rounded-corners">
        <span><?php echo $vars['sumcalls']['calls'] ?></span><br />
        <?php echo Text::get('node-side-sumcalls-calls'); ?>
    </div>
    <div class="half campaigns rounded-corners last">
        <span><?php echo $vars['sumcalls']['campaigns'] ?></span><br />
        <?php echo Text::get('node-side-sumcalls-campaigns'); ?>
    </div>
</div>
