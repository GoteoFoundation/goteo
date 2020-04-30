<div class="section download">
	<div class="container">
		<div class="row">
			<div class="col-md-1 col-xs-3">
				<img class="pdf" src="/assets/img/icons/channel-call/pdf.svg" width="55px">
			</div>
			<div class="col-md-11 col-xs-9">
				<h2 class="title"><?= $this->channel->terms_download_title ?></h3>
				<div class="description">
					<?= $this->channel->terms_download_description ?>
				</div>
				<a href="<?= $this->channel->terms_download_url ?>" class="btn btn-transparent pull-right">
					<i class="icon icon-download icon-2x"></i><?= $this->text('regular-download') ?>
				</a>
			</div>
	</div>
</div>