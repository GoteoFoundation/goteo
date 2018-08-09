<?php

$this->layout('layout');

$this->section('content');

$user=$this->user;

?>

<div class="user">
	<div class="section main-info">
		<div class="container">
			<div class="row">
				<div class="col-md-2 avatar">
					<img src="<?= $user->avatar->getLink(100, 100, true) ?>" class="img-circle" >		
				</div>
				<div class="col-md-7 info">
					<div class="user-label">
						<?= $this->text('profile-name-header') ?>
					</div>
					<div class="name">
						<?= $user->name ?>
					</div>
				</div>
				<div class="col-md-3">
					
				</div>
			</div>
		</div>
	</div>
</div>


<?php $this->replace() ?>