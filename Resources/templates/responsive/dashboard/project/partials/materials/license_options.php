<?php foreach($this->licenses as $license): ?>
	<option value="<?= $license->id ?>"><?= $license->name ?></option>
<?php endforeach; ?>