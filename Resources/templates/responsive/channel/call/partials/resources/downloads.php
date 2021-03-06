<?php if($this->resources): ?>

<div class="section resource-download">	
	<div class="container">

<?php foreach ($this->resources as $item): ?>
		<?php $image=$item->getImage(); ?>
		<?php $category=$item->getCategory(); ?>
		<?php $icon=$category->getIcon(); ?>
		<div class="row item">
			<div class="col-md-2 col-xs-12 img">
				<img class="img-responsive" src="<?= $image->getLink(400, 300, true) ?>">
			</div>
			<div class="col-md-8 col-xs-12 content">
				<h2 class="category">
					<img src="<?= $icon->getLink(0, 25, false)?>"> <?= $category->name ?></h2>
				<div class="description">
					<h3 class="title"><?= $item->title ?></h2>
					<?= $item->description ?>
				</div>
				<a href="<?= $item->action_url ?>" class="btn btn-transparent pull-right">
					<img class="pdf" src="/assets/img/icons/channel-call/pdf.svg" width="20px">
					<?= $item->action ? $item->action : $this->text('regular-download').' .PDF' ?>
				</a>
			</div>
		</div>

<?php endforeach ?>

	</div>
</div>

<?php endif ?>