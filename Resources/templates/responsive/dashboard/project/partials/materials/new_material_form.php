<div id="new-material-form" class="collapse">
    <form class="spacer" name="new-material-form" method="post">
        <label class="spacer-10" >
        <?= $this->text('dashboard-new-material-form-material') ?>
        </label>
        <input class="form-control" type="text" name="material" id="new-material-material" required value="">
        <label class="spacer-10" >
            <?= $this->text('dashboard-new-material-form-description') ?>
        </label>
        <textarea name="description" class="form-control" id="new-material-description" required rows="3"></textarea>
        <label class="spacer-10" >
            <?= $this->text('dashboard-new-material-form-type') ?>
        </label>
        <select class="form-control" name="icon" id="new-material-icon" >
            <option value="">-- Selecciona un tipo --</option>
            <?php if($this->icons): ?>
                <?php foreach($this->icons as $icon): ?>
                    <option value="<?= $icon->id ?>"><?= $icon->name ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <div id="license-group">
            <label class="spacer-10" >
                <?= $this->text('dashboard-new-material-form-license') ?>
            </label>
             <select class="form-control" name="license" id="new-material-license">
                    <option value="">-- Selecciona un tipo --</option>
            </select>
        </div>
        <label class="spacer-10" >
        <?= $this->text('dashboard-new-material-form-url') ?>
        </label>
        <input class="form-control" type="text" name="url" id="new-material-url" value="">
        <div class="row spacer">
            <div class="col-md-6">
                <button type="button" id="btn-save-new-material" class="btn btn-block btn-success"><?= $this->text('regular-save') ?></button>
            </div>
        </div>
    </form>
</div>
