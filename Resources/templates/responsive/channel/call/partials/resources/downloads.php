<?php if($this->resources): ?>
<div class="section resource-download">	
	<div class="container">

<?php foreach ($this->resources as $item): ?>
	
		<div class="row item">
			<div class="col-md-1 col-xs-3 img">
				<img class="pdf" src="/assets/img/icons/channel-call/pdf.svg" width="55px">
			</div>
			<div class="col-md-9 col-xs-9 content">
				<h2 class="category"><?= $item->title ?></h2>
				<div class="description">
					<h3 class="title"><?= $item->title ?></h2>
					<?= $item->description ?>
				</div>
				<a href="<?= $item->url ?>" class="btn btn-transparent pull-right">
					<img class="pdf" src="/assets/img/icons/channel-call/pdf.svg" width="20px"><?= $this->text('regular-download').' .PDF' ?>
				</a>
			</div>
		</div>

<?php endforeach ?>

	</div>
</div>

<?php endif ?>