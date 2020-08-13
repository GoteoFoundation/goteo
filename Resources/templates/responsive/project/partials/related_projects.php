<aside class="related-projects">
    <div class="container-fluid">
		<h2 class="green-title">
		<?= $this->text('project-related') ?>
		</h2>

		<div class="row">
	    <?php foreach ($this->related_projects as $related_project) : ?>

	              <div class="col-sm-6 col-md-4 col-xs-12 spacer">
	                <?= $this->insert('project/widgets/normal', ['project' => $related_project, 'admin' => false]) ?>
	              </div>
	    <?php endforeach ?>
    	</div>

    </div>
</aside>