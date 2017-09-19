<?php $this->layout('home/layout') ?>

<?php $this->section('sidebar-header') ?>
    <span class="header-title">
    	<?= $this->text('home-menu-sidebar-header-title') ?>
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

<?= $this->insert('home/partials/search') ?>

<?= $this->insert('home/partials/adventages') ?>

<?= $this->insert('home/partials/matchfunding') ?>

<?= $this->insert('home/partials/foundation') ?>

<?= $this->insert('home/partials/tools') ?>


<?php $this->replace() ?>