<div class="box summary">
	<div class="row">
		<div class="col-md-2">
			<div>
				<?= \amount_format($this->summary['projects'], 0, true) ?>
			</div>
			<div>
				<?= $this->text('regular-projects'); ?>
			</div>
		</div>
		<div class="col-md-2">
			<div>
				<?= \amount_format($this->summary['active'], 0, true) ?>
			</div>
			<div>
				<?= $this->text('node-side-summary-active'); ?>
			</div>
		</div>
		<div class="col-md-2">
			 <div>
			 	<?= \amount_format($this->summary['success'], 0, true) ?>
			 </div>
			 <div>
			 	<?= $this->text('node-side-summary-success'); ?>
			 </div>
		</div>
		<div class="col-md-2">
			 <div>
			 	<?= \amount_format($this->summary['investors'], 0, true) ?>
			 </div>
			 <div>
			 	<?= $this->text('node-side-summary-investors'); ?>
			 </div>
		</div>
		<div class="col-md-2">
			 <div>
			 	<?= \amount_format($this->summary['supporters'], 0, true) ?>
			 </div>
			 <div>
			 	<?= $this->text('node-side-summary-supporters'); ?>
			 </div>
		</div>
		<div class="col-md-2">
			 <div>
			 	<?= \amount_format($this->summary['amount'], 0, true) ?>
			 </div>
			 <div>
			 	<?= $this->text('node-side-summary-amount'); ?>
			 </div>
		</div>
	</div>
</div>