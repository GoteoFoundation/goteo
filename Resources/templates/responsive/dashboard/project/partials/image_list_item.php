<li>
    <div class="image" style="background-image:url(<?= $this->image_url ?>)"></div>
    <div class="options">
        <a class="btn <?= $this->project->image->name == $this->image_name ? 'btn-pink' : 'btn-default' ?>"><i class="fa fa-star" title="<?= $this->text('dashboard-project-default-image') ?>"></i></a>
        <a class="btn btn-danger" onclick="return confirm('<?= $this->ee($this->text('dashboard-project-delete-image-confirm'), 'js') ?>')"><i class="fa fa-trash" title="<?= $this->text('dashboard-project-delete-image') ?>"></i></a>
    </div>
</li>
