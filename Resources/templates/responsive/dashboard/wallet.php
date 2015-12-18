<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

    <div class="row user-pool">
	   	<div class="col-sm-3 hidden-xs img-pool">
	   		<img src="<?= SRC_URL . '/assets/img/dashboard/pool.png' ?>" class="img-responsive">
	   	</div>
	   	<div class="col-sm-9 col-xs-12 user-pool-info margin-2">
	   		<h2 class="col-md-8 col-sm-12 col-xs-12"><?=  $this->text('dashboard-my-wallet-available', amount_format($this->pool->getAmount())) ?></h2>
	   		<div class="pool-conditions col-md-8 col-sm-12 clear-both">
	   		<?= $this->text('dashboard-my-wallet-pool-info') ?> <a data-toggle="modal" data-target="#poolModal" href=""><?= $this->text('regular-here') ?></a>
	   		</div>
	   		<div class="row extra-info margin-2 spacer clear-both">
	   			<a href="/discover" class="text-decoration-none" >
					<div class="col-xs-6 col-sm-3">
						<button type="button" class="btn btn-block col-xs-3 margin-2 donor"><?= $this->text('dashboard-my-wallet-contribute-button') ?></button>
					</div>
				</a>
				<a href="/pool" class="text-decoration-none">
					<div class="col-xs-6 col-sm-3">
						<button type="button" class="btn btn-block col-xs-3 margin-2 chargue"><?= $this->text('recharge-button') ?></button>
					</div>
				</a>
	   		</div>
	   	</div>
	</div>

	<?php if($this->projects_suggestion): ?>

	<div class="container general-dashboard">
	<h2><?= $this->text('profile-suggest-projects-interest') ?></h2>
		<?php foreach ($this->projects_suggestion as $group=>$projects) : ?>
			<div class="row spacer">
			<?php foreach ($projects['items'] as $project) : ?>
					<div class="col-md-4 margin-2">
						<?= $this->text_widget('/widget/project/' . $project->id) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if($group==2)
            		break;
            ?>
		<?php endforeach; ?>
	</div>

	<?php else: ?>

		<div class="container general-dashboard">
			<h2><?= $this->text('discover-group-popular-header') ?></h2>
				<div class="row spacer">
				<?php foreach ($this->popular_projects as $key => $project) : ?>
						<div class="col-md-4 margin-2">
							<?= $this->text_widget('/widget/project/' . $project->id) ?>
	                    </div>
	                    <?php if($key==2): ?>
			            	</div>
			            	<!-- second row -->
			            	<div class="row spacer">
		            	<?php endif; ?>
	                <?php endforeach; ?>
	            </div>

		</div>

	<?php endif ?>

<!-- Modal -->
<div class="modal fade" id="poolModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $this->text('invest-modal-pool-title') ?></h4>
      </div>
      <div class="modal-body">
        <?= $this->text('dashboard-my-wallet-modal-pool-info') ?>
      </div>
    </div>
  </div>
</div>
<?php $this->replace() ?>
