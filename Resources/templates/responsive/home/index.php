<?php $this->layout('home/layout') ?>

<?php $this->section('sidebar-header') ?>
    <span class="header-title">
    	<?= $this->text('home-menu-toggle-label')."..." ?>
    </span>
<?php $this->replace() ?>

<?php $this->section('sidebar-footer') ?>
    <a href="/project/create" class="btn btn-fashion">
		<?= $this->text('regular-create') ?>
	</a>
	<a href="/login" class="btn btn-light-black">
		<?= $this->text('login-title') ?>
	</a>
<?php $this->replace() ?>

<?php $this->section('sidebar-menu-toggle') ?>
    <?= $this->text('home-menu-toggle-label') ?> <i class="fa fa-angle-right"></i>
<?php $this->replace() ?>



<?php $this->section('home-content') ?>

<!-- Banner section -->

<?= $this->insert('home/partials/main_slider') ?>

<?= $this->insert('home/partials/projects') ?>

<?= $this->insert('home/partials/call_to_action') ?>

<?= $this->insert('home/partials/advantages') ?>

<?= $this->insert('home/partials/matchfunding') ?>

<?= $this->insert('home/partials/foundation') ?>

<?= $this->insert('home/partials/channels') ?>

<?= $this->insert('home/partials/tools') ?>

<!-- Wallet Modal -->
    <div class="modal fade" id="WalletVideoModal" tabindex="-1" role="dialog" aria-labelledby="WalletVideoModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-body">
          	<div class="close-button">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="embed-responsive embed-responsive-16by9">
            	<iframe src="https://player.vimeo.com/video/246988506" width="100%" height="341" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->

    <!-- Wallet Modal -->
    <div class="modal fade" id="DashboardVideoModal" tabindex="-1" role="dialog" aria-labelledby="DashboardVideoModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-body">
          	<div class="close-button">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="embed-responsive embed-responsive-16by9">
            	<iframe src="https://player.vimeo.com/video/238942023" width="100%" height="341" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->

<?php $this->replace() ?>


