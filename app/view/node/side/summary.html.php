<?php
use Goteo\Library\Text,
    Goteo\Core\View;
?>
<div class="side_widget summary">
    <div class="line rounded-corners">
    	<p class="text"><?php echo Text::get('regular-total'); ?></p>
        <p class="quantity projects">
           	<?php echo \amount_format($this['summary']['projects'], 0, true) ?><span class="text"><?php echo Text::get('regular-projects'); ?></span>
        </p>
    </div>
    <div class="half rounded-corners">
    	<p class="text"><?php echo Text::get('node-side-summary-active'); ?></p>
        <p class="quantity active"><?php echo \amount_format($this['summary']['active'], 0, true) ?></p>
    </div>
    <div class="half rounded-corners last">
    	<p class="text"><?php echo Text::get('node-side-summary-success'); ?></p>
        <p class="quantity success"><?php echo \amount_format($this['summary']['success'], 0, true) ?></p>
    </div>
    <div class="half rounded-corners">
    	<p class="text"><?php echo Text::get('node-side-summary-investors'); ?></p>
        <p class="quantity investors"><?php echo \amount_format($this['summary']['investors'], 0, true) ?></p>
    </div>
    <div class="half rounded-corners last">
    	<p class="text"><?php echo Text::get('node-side-summary-supporters'); ?></p>
        <p class="quantity supporters"><?php echo \amount_format($this['summary']['supporters'], 0, true) ?></p>
    </div>
    <div class="line rounded-corners">
    	<p class="text"><?php echo Text::get('node-side-summary-amount'); ?></p>
        <p class="quantity amount violet"><span><?php echo \amount_format($this['summary']['amount']) ?></span></p>
    </div>
</div>
