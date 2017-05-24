
<?php if($this->interests || $this->projects): ?>
<div class="<?= $this->autoUpdate ? 'auto-update-projects-interests' : '' ?>">
    <h2><?= $this->text('profile-suggest-projects-interest') ?></h2>
<?php else: return ?>

<?php endif ?>

<?php if($this->interests): ?>
    <div class="row interests spacer">
        <?php foreach ($this->interests as $key => $interest): ?>
          <div class="col-sm-3 col-xs-6">
            <label>
              <input type="checkbox" class="no-margin-checkbox big-checkbox interest" name="interest-<?= $key ?>" id="<?= $key ?>" <?= isset($this->user_interests[$key]) ? 'checked=checked' : '' ?>)>
              <span style="margin-left:5px; font-weight:normal;"><?= $interest ?></span>
            </label>
          </div>
        <?php endforeach ?>
    </div>
<?php endif ?>


<?php if($this->projects): ?>
    <div class="row spacer">
        <?php foreach ($this->projects as $project) : ?>
              <div class="col-sm-6 col-md-4 col-xs-12 spacer">
                <?= $this->insert('project/widget.php', ['project' => $project]) ?>
              </div>
        <?php endforeach ?>
    </div>

    <?php if($this->showMore): ?>
        <div class="row more-projects-button">
            <div class="col-sm-2 button">
                <button class="btn btn-block dark-grey"><?= $this->text('regular-load-more') ?></button>
            </div>
        </div>
    <?php endif ?>
<?php endif ?>

</div>
