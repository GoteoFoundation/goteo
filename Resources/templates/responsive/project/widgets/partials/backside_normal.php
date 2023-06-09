<div class="backside" id="backflip-<?= $this->project->id ?>">
    <button class="close flip" href="#backflip-<?= $this->project->id ?>"><i class="icon icon-close"></i></button>

    <?php if ($this->project->isPermanent()): ?>
        <?= $this->insert('project/widgets/partials/data_list_permanent', ['project' => $this->project]) ?>
    <?php else: ?>
        <?= $this->insert('project/widgets/partials/data_list') ?>
    <?php endif; ?>

    <?php if($this->project->inCampaign()): ?>
    <div class="invest">
        <a class="btn btn-lg btn-block btn-lilac" target="_blank" href="/invest/<?= $this->project->id ?>"><?= $this->text('project-regular-support') ?></a>
    </div>
    <?php endif ?>
</div>
