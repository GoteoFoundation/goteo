<div class="custom-header">
	<div class="pull-left">
		<a href="<?= '/channel/'.$this->channel->id . $this->lang_url_query($this->lang_current())?> ">
			<img src="<?= $this->channel->logo ? $this->channel->logo->getlink(0,40) : '' ?>" height="40px">
		</a>
	</div>
	<div class="pull-right hidden-xs">
		<span style="<?= $this->colors['header'] ? "color:".$this->colors['header'] : '' ?> ">
			<?= $this->text('call-header-powered-by') ?>	
		</span>
		<a href="<?= $this->get_config('url.main') ?>">
			<?php if($this->colors['header_logo']=="blue"): ?>
					<img height="30" src="<?= '/assets/img/goteo-blue-green.svg' ?>" >
			<?php else: ?>
					<img height="30" src="<?= '/assets/img/goteo-white-green.png' ?>" >
			<?php endif; ?>
		</a>
	</div>
	<div id="navbar" class="navbar languages">
		<div class="active">
			<span style="<?= $this->colors['header'] ? "color:".$this->colors['header'] : '' ?> "><?= $this->lang_name($this->lang_current()) ?></span>
			<span class="glyphicon glyphicon glyphicon-menu-down" aria-hidden="true"></span>
		</div>
		<ul class="languages-list list-unstyled">
		<?php foreach($this->lang_list('name') as $key => $lang): ?>
			<?php if ($this->lang_active($key)) continue; ?>
				<li>
				<a href="<?= $this->lang_url_query($key) ?>">
					<?= $lang ?>
				</a>
				</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>