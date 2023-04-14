<li data-name="<?= $this->image_name ?>">
    <div class="image" style="background-image:url(<?= $this->image_url ?>)"></div>
    <?php if(!$this->idle): ?>
      <div class="options">
        <a class="delete-image btn btn-default"><i class="fa fa-trash" title="<?= $this->text('dashboard-project-delete-image') ?>"></i></a>
      </div>
    <?php endif ?>
</li>
