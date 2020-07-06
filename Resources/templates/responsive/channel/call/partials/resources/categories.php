<?php if($this->resources_categories): ?>

	<ul>
	<?php foreach ($this->resources_categories as $item): ?>
			<li>
				<?= $item->name ?>
			</li>

	<?php endforeach ?>
	</ul>

<?php endif ?>