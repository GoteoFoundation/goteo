<div class="backside call" id="backflip-<?= $this->project->id ?>">
    <a class="close flip" href="#backflip-<?= $this->project->id ?>"><i class="icon icon-close"></i></a>

    <div class="data-list title">
        <h5 title="<?= $this->call->name ?>">
            <i class="icon icon-call"></i> <?= $this->text('regular-call') ?> x<strong>2</strong>
            <img src="<?= $this->call->user->avatar->getLink(100,50) ?>">
        </h5>
    </div>

    <?= $this->insert('project/widgets/partials/data_list') ?>

    <?php if($this->project->inCampaign()): ?>
    <div class="invest">
        <a class="btn btn-lg btn-block btn-white" target="_blank" href="/invest/<?= $this->project->id ?>"><?= $this->text('project-regular-support') ?></a>
    </div>
    <?php endif ?>
</div>
