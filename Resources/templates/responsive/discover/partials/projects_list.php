<div class="section projects-list" >
	<div class="container">
	<?php if ($this->projects) : ?>
	    <?php foreach ($this->projects as $project): ?>
	        <div class="col-sm-6 col-md-4 col-xs-12 spacer widget-element">
	            <?= $this->insert('project/widgets/normal', [
	                'project' => $project,
	                'admin' => (bool)$this->admin
	            ]) ?>
	        </div>
	    <?php endforeach; ?>
	<?php else : ?>
		<h3 class="no-results">
		<?= $this->text('discover-results-empty') ?>
		</h3>
	<?php endif ?>
	</div>
</div>
