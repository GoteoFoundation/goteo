<div class="project-widget micro" id="project-<?= $this->project->id ?>">
    <a class="img-link" href="/project/<?= $this->project->id ?>">
        <img class="img-project" src="<?= $this->project->image->getLink(240, 240, true); ?>">
    </a>
    <div class="content">
        <?php if($this->admin): ?>
            <span class="label label-danger"><?= $this->project->getTextStatus() ?></span>
        <?php endif ?>
        <div class="title"><a href="/project/<?= $this->project->id ?>"><?= $this->text_truncate($this->project->name, 80); ?></a></div>
    </div>
</div>
