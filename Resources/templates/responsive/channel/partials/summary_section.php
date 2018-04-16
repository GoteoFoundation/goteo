<?php if(!$this->summary) return; ?>
<div class="summary-section">
	<div class="container-fluid summary">
		<!--<h2 class="title-section">
			<?= $this->text('node-stats-title') ?>
		</h2>-->
		<div class="row">
			<div class="col-sm-3 item">
				 <div>
				 	<?= amount_format($this->summary['projects'], 0, true) ?>
				 </div>
				 <div class="description">
				 	<?= $this->text('regular-projects') ?>
				 </div>
			</div>
			<div class="col-sm-3 item">
				 <div>
				 	<?= amount_format($this->summary['success'], 0, true) ?>
				 </div>
				 <div class="description">
				 	<?= $this->text('node-side-summary-success'); ?>
				 </div>
			</div>
			<div class="col-sm-3 item">
				 <div>
				 	<?= amount_format($this->summary['investors'], 0, true) ?>
				 </div>
				 <div class="description">
				 	<?= $this->text('node-side-summary-investors'); ?>
				 </div>
			</div>
			<div class="col-sm-3 item">
				 <div>
				 	<?= amount_format($this->summary['amount']) ?>
				 </div>
				 <div class="description">
				 	<?= $this->text('node-side-summary-amount'); ?>
				 </div>
			</div>
		</div>
	</div>
</div>
