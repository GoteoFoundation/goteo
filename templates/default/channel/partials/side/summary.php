<div class="side_widget summary">
    <div class="line rounded-corners">
    	<p class="text"><?= $this->text('regular-total') ?></p>
        <p class="quantity projects">
           	<?= \amount_format($this->summary['projects'], 0, true) ?><span class="text"><?= $this->text('regular-projects'); ?></span>
        </p>
    </div>
    <div class="half rounded-corners">
    	<p class="text"><?= $this->text('node-side-summary-active'); ?></p>
        <p class="quantity active"><?= \amount_format($this->summary['active'], 0, true) ?></p>
    </div>
    <div class="half rounded-corners last">
    	<p class="text"><?= $this->text('node-side-summary-success'); ?></p>
        <p class="quantity success"><?= \amount_format($this->summary['success'], 0, true) ?></p>
    </div>
    <div class="half rounded-corners">
    	<p class="text"><?= $this->text('node-side-summary-investors'); ?></p>
        <p class="quantity investors"><?= \amount_format($this->summary['investors'], 0, true) ?></p>
    </div>
    <div class="half rounded-corners last">
    	<p class="text"><?= $this->text('node-side-summary-supporters'); ?></p>
        <p class="quantity supporters"><?= \amount_format($this->summary['supporters'], 0, true) ?></p>
    </div>
    <div class="line rounded-corners">
    	<p class="text"><?= $this->text('node-side-summary-amount'); ?></p>
        <p class="quantity amount violet"><span><?= \amount_format($this->summary['amount']) ?></span></p>
    </div>
</div>
