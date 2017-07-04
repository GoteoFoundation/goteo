<div class="form-group">
    <label for="form-<?= $this->name ?>"><?= $this->name?></label>
    <?= $this->html('text', [
        'name' => $this->name,
        'attribs' => [
            'class' => 'form-control',
            'id' => 'form-' . $this->name,
            'placeholder' => $this->name ]])
     ?>
</div>
