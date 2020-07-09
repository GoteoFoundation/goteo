<?php if($this->resources): ?>

<div class="section resource-download">	
	<div class="container">

<?php foreach ($this->resources as $item): ?>
		<?php $image=$item->getImage(); ?>
		<div class="row item">
			<div class="col-md-2 col-xs-12 img">
				<img class="responsive" src="<?= $image->getLink(200, 150, true) ?>">
			</div>
			<div class="col-md-8 col-xs-12 content">
				<h2 class="category">
					<i class="icon icon-resource-<?= $item->getCategory()->icon ?> icon-2x"></i> <?= $item->getCategory()->name ?></h2>
				<div class="description">
					<h3 class="title"><?= $item->title ?></h2>
					<?= $item->description ?>
				</div>
				<a href="<?= $item->action_url ?>" class="btn btn-transparent pull-right">
					<img class="pdf" src="/assets/img/icons/channel-call/pdf.svg" width="20px"><?= $this->text('regular-download').' .PDF' ?>
				</a>
			</div>
		</div>

<?php endforeach ?>

	</div>
</div>

<?php endif ?>