<div class="summary-matchfunding-section" >
	<div class="container-fluid summary">
		<!--<h2 class="section-title">
			<?= $this->text('node-stats-title') ?>
		</h2>-->
		<div class="row">
			<div class="col-sm-4 item">
				<div>
					€ 82.000
				</div>
				<div class="description">
					Quedan por asignar
				</div>
			</div>
			<div class="col-sm-4 item matchfunding">
				<div>
					€ 100.000
				</div>
				<div class="description">
					Dinero destinado a proyectos
				</div>
			</div>
			<div class="col-sm-4 item">
				 <div>
				 	<?= \amount_format($this->summary['amount'], 0, true) ?>
				 </div>
				 <div class="description">
				 	Aportado por la ciudadanía
				 </div>
			</div>
		</div>
	</div>
</div>