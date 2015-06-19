<?php
use Goteo\Library\Text,
    Goteo\Core\View;

// ver página de ver mas convocatorias
?>
<div class="side_widget convocatorias activable">
    <div class="block rounded-corners">
        <p class="title"><?= $this->text('node-side-sumcalls-header') ?></p>
        <div style="margin-bottom:6px">
        	<p class="text"><?= $this->text('node-side-sumcalls-budget') ?></p>
    	    <p class="quantity all"><span><?= \amount_format($this->sumcalls['budget']) ?></span></p>
        </div>
        <div>
	        <p class="text"><?=$this->text('node-side-sumcalls-rest') ?></p>
	        <p class="quantity rest"><span><?= \amount_format($this->sumcalls['rest']) ?></span></p>
        </div>
    </div>
    <div class="half calls rounded-corners">
        <span><?= $this->sumcalls['calls'] ?></span><br />
        <?= $this->text('node-side-sumcalls-calls') ?>
    </div>
    <div class="half campaigns rounded-corners last">
        <span><?= $this->sumcalls['campaigns'] ?></span><br />
        <?= $this->text('node-side-sumcalls-campaigns') ?>
    </div>
</div>
