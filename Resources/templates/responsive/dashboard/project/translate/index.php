<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-project') ?>

    <?php if($this->available): ?>
        <p><?= $this->text('dashboard-translate-project-currents', '<strong>' . count($this->available) . '</strong>') ?></p>
        <div class="list-group">
        <?php foreach($this->available as $lang): ?>
                <a class="list-group-item" href="/dashboard/project/<?= $this->project->id ?>/translate/<?= $lang ?>">
                    <i class="icon icon-edit" title="<?= $this->text('regular-edit') ?>"></i>
                    <?= $this->languages[$lang] ?>
                    <span class="badge"><?= $this->text('translator-percent', rand(1,99)) ?></span>
                </a>
        <?php endforeach ?>
        </div>
    <?php else: ?>
        <p class="text-danger"><strong><?= $this->text('dashboard-translate-project-empty') ?></strong></p>
    <?php endif ?>

<?php print_r($this->languages) ?>
<?php print_r($this->available) ?>

<?php $this->replace() ?>
