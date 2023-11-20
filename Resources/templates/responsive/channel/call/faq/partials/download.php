<?php if($this->downloads): ?>
<?php foreach ($this->downloads as $item): ?>
	
<div class="section download">
	<div class="container">
		<div class="row">
			<div class="col-md-1 col-xs-3">
				<img class="pdf" src="/assets/img/icons/channel-call/pdf.svg" width="55px">
			</div>
			<div class="col-md-11 col-xs-9">
				<h2 class="title"><?= $item->title ?></h2>
				<div class="description">
					<?= $item->description ?>
				</div>
				<a href="<?= $item->url ?>" class="btn btn-transparent pull-right">
					<i class="icon icon-download icon-2x"></i><?= $this->text('regular-download') ?>
				</a>
			</div>
		</div>
	</div>
</div>

<?php endforeach ?>
<?php endif ?>