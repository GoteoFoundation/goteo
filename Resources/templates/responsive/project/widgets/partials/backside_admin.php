<div class="backside admin" id="backflip-<?= $this->project->id ?>">
    <div class="close flip" href="#backflip-<?= $this->project->id ?>"><i class="icon icon-close"></i></div>

    <a href="/project/<?= $this->project->id ?>" class="floating" title="<?= $this->text('regular-preview') ?>"><i class="icon icon-preview"></i></a>

    <div class="content">

        <div class="bottom">
            <a class="btn btn-lg btn-block btn-white" href="/dashboard/project/<?= $this->project->id ?>"><?= $this->text('dashboard-menu-activity-summary') ?></a>
            <a class="btn btn-lg btn-block btn-white" href="/dashboard/project/<?= $this->project->id ?>/images"><?= $this->text('images-main-header') ?></a>
            <a class="btn btn-lg btn-block btn-cyan" href="/project/edit/<?= $this->project->id ?>"><?= $this->text('regular-edit') ?></a>
        </div>
    </div>
</div>
