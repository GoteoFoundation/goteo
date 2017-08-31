<div id="new-material-form" class="collapse">
<blockquote>
    <form class="form spacer" name="new-material-form" method="post">
        <div class="form-group">
            <div class="input-wrap">
                <label>
                <?= $this->text('dashboard-new-material-form-material') ?>
                </label>
                <input required="required" class="form-control" type="text" name="material" id="new-material-material" required value="">
            </div>
        </div>
        <div class="form-group">
            <div class="input-wrap">
                <label>
                    <?= $this->text('dashboard-new-material-form-description') ?>
                </label>
                <textarea required="required" name="description" class="form-control" id="new-material-description" required rows="3"></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="input-wrap">
                <label>
                    <?= $this->text('dashboard-new-material-form-type') ?>
                </label>
                <select required="required" class="form-control" name="icon" id="new-material-icon" >
                    <option value="">-- Selecciona un tipo --</option>
                    <?php if($this->icons): ?>
                        <?php foreach($this->icons as $icon): ?>
                            <option value="<?= $icon->id ?>"><?= $icon->name ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="input-wrap">
                <div required="required" id="license-group">
                    <label>
                        <?= $this->text('dashboard-new-material-form-license') ?>
                    </label>
                     <select class="form-control" name="license" id="new-material-license">
                            <option value="">-- Selecciona un tipo --</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="input-wrap">
                <label>
                <?= $this->text('dashboard-new-material-form-url') ?>
                </label>
                <input class="form-control" type="text" name="url" id="new-material-url" value="">
            </div>
        </div>
        <div class="form-group">
            <button type="submit" id="btn-save-new-material" class="btn btn-lg btn-cyan"><?= $this->text('regular-save') ?></button>
        </div>
    </form>
</blockquote>
</div>
