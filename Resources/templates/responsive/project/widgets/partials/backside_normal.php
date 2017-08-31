<div class="backside" id="backflip-<?= $this->project->id ?>">
    <div class="close flip" href="#backflip-<?= $this->project->id ?>"><i class="icon icon-close"></i></div>

    <?= $this->insert('project/widgets/partials/data_list') ?>

    <?php if($this->project->inCampaign()): ?>
    <div class="invest">
        <a class="btn btn-lg btn-block btn-lilac" href="/invest/<?= $this->project->id ?>"><?= $this->text('project-regular-support') ?></a>
    </div>
    <?php endif ?>
</div>
