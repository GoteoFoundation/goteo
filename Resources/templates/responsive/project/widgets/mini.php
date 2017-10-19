<div class="project-widget mini" id="project-<?= $this->project->id ?>">
    <a class="img-link" href="/project/<?= $this->project->id ?>">
        <img class="img-project" src="<?= $this->project->image->getLink(240, 135, true); ?>">
        <div class="status-mark">
            <span class="label"><?= amount_format($this->project->invested) ?> / <?= amount_format($this->project->amount) ?></span>
        <?php if($this->admin): ?>
            <span class="label label-danger pull-right"><?= $this->project->getTextStatus() ?></span>
        <?php endif ?>
        </div>
    </a>
    <div class="content">
        <div class="title"><a href="/project/<?= $this->project->id ?>"><?= $this->text_truncate($this->project->name, 80); ?></a></div>
    </div>
</div>
