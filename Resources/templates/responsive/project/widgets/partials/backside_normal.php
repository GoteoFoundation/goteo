<div class="backside" id="backflip-<?= $this->project->id ?>">
    <a class="close flip" href="#backflip-<?= $this->project->id ?>"><i class="icon icon-close"></i></a>

    <?= $this->insert('project/widgets/partials/data_list') ?>

    <?php if($this->project->inCampaign()): ?>
    <div class="invest">
        <a class="btn btn-lg btn-block btn-lilac" target="_blank" href="/invest/<?= $this->project->id ?>"><?= $this->text('project-regular-support') ?></a>
    </div>
    <?php endif ?>
</div>
