<?php if($this->resources_categories): ?>
<div class="section resources-categories">
	<div class="container">
		<div class="row">
			<ul class="list-inline">
				<li <?= $this->category ? '' : 'class="selected"' ?>>
					<a href="<?= '/channel/'.$this->channel->id.'/resources' ?>">
						<?= $this->text('regular-all') ?>
					</a>
				</li>
			<?php foreach ($this->resources_categories as $item): ?>
					<li <?= $this->category!=$item->id ? '' : 'class="selected"' ?>>
						<a href="<?= '/channel/'.$this->channel->id.'/resources/'.$item->slug ?>">
							<i class="icon icon-resource-<?= $item->icon ?> icon-2x"></i> <?= $item->name ?>
						</a>
					</li>

			<?php endforeach ?>
			</ul>
		</div>
	</div>
</div>
<?php endif ?>