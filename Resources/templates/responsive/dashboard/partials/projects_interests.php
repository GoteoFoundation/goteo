
<?php if($this->auto_update): ?><div class="auto-update-projects" data-url="<?= $this->auto_update ?>" data-total="<?= $this->total ?>" data-limit="<?= $this->limit ?>"><?php endif ?>

<?php if($this->interests): ?>
    <div class="row interests spacer">
        <?php foreach ($this->interests as $key => $interest): ?>
          <div class="col-sm-4 col-xs-6">
            <label>
              <input type="checkbox" class="no-margin-checkbox big-checkbox interest" name="interest-<?= $key ?>" id="<?= $key ?>"<?= isset($this->user_interests[$key]) ? ' checked="checked"' : '' ?>>
              <span style="margin-left:5px; font-weight:normal;"><?= $interest ?></span>
            </label>
          </div>
        <?php endforeach ?>
    </div>
<?php endif ?>


<?php if($this->projects): ?>
    <div class="row spacer elements-container">
        <?= $this->insert('dashboard/partials/projects_widgets_list', ['admin' => (bool)$this->admin]) ?>
    </div>

    <div class="spacer-20 spacer-bottom text-center more-projects-button<?= $this->auto_update && $this->total > count($this->projects) ? '' : ' hidden' ?>">
        <button class="btn btn-link"><?= $this->text('regular-load-more') ?> &nbsp; &nbsp; <i class="fa fa-chevron-right"></i></button>
    </div>
<?php endif ?>

<?php if($this->auto_update): ?></div><?php endif ?>
