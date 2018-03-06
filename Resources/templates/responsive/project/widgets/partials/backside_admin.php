<div class="backside admin" id="backflip-<?= $this->project->id ?>">
    <a class="close flip" href="#backflip-<?= $this->project->id ?>"><i class="icon icon-close"></i></a>

    <a href="/project/<?= $this->project->id ?>" class="floating" title="<?= $this->text('regular-preview') ?>"><i class="icon icon-preview"></i></a>

    <div class="content">

        <div class="bottom">
            <a class="btn btn-lg btn-block btn-white" href="/dashboard/project/<?= $this->project->id ?>"><?= $this->text('dashboard-menu-activity-summary') ?></a>
            <a class="btn btn-lg btn-block btn-white" href="/dashboard/project/<?= $this->project->id ?>/images"><?= $this->text('images-main-header') ?></a>
            <?php if($this->project->isApproved()): ?>
                <a class="btn btn-lg btn-block btn-cyan" href="/dashboard/project/<?= $this->project->id ?>/updates"><?= $this->text('dashboard-menu-projects-updates') ?></a>
            <?php else: ?>
                <a class="btn btn-lg btn-block btn-cyan" href="/dashboard/project/<?= $this->project->id ?>/overview"><?= $this->text('regular-edit') ?></a>
            <?php endif ?>
        </div>
    </div>
</div>
