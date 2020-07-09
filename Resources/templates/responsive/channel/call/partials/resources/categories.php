<?php if($this->resources_categories): ?>
<div class="section resources-categories">
	<div class="container">
		<div class="row">
			<ul class="list-inline">
				<li class="selected">
					<?= $this->text('regular-all') ?>
				</li>
			<?php foreach ($this->resources_categories as $item): ?>
					<li>
						<i class="icon icon-resource-<?= $item->icon ?> icon-2x"></i> <?= $item->name ?>
					</li>

			<?php endforeach ?>
			</ul>
		</div>
	</div>
</div>
<?php endif ?>