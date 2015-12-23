<h2><?= $this->text('profile-suggest-projects-interest') ?></h2>
    <div class="row interests spacer">
      <?php foreach ($this->interests as $key => $interest): ?>
      <div class="col-sm-3 col-xs-6">
        <label>
          <input type="checkbox" class="no-margin-checkbox big-checkbox interest" name="interest-<?= $key ?>" id="<?= $key ?>" <?= isset($this->user_interests[$key]) ? 'checked=checked' : '' ?>)>         
          <span style="margin-left:5px; font-weight:normal;"><?= $interest ?></span>                    
        </label>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="row spacer">
    <?php foreach ($this->projects_suggestion as $key => $project) : ?>
            
              <div class="col-sm-6 col-md-4 col-xs-12 spacer">
                <?= $this->insert('projects/widget.php', ['project' => $project]) ?>
              </div>
    <?php endforeach; ?>
    </div>
