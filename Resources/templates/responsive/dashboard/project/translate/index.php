<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-project') ?>

    <?php if($this->available): ?>
        <p><?= $this->text('dashboard-translate-project-currents', [
                '%LANG%' => '<strong><em>' . $this->languages[$this->project->lang] . '</em></strong>',
                '%NUM%' => '<strong>' . count($this->available) . '</strong>'
                ]) ?></p>
        <div class="list-group">
        <?php foreach($this->available as $lang):
            $percent = $this->project->getLangsPercent($lang);
        ?>
                <a class="list-group-item" href="/dashboard/project/<?= $this->project->id ?>/translate/<?= $lang ?>">
                    <i class="icon icon-edit" title="<?= $this->text('regular-edit') ?>"></i>
                    <?= $this->languages[$lang] ?>
                    <?= $this->percent_badge($percent, $this->text('translator-percent', $percent)) ?>
                </a>
        <?php endforeach ?>
        </div>
    <?php else: ?>
        <p class="text-danger"><strong><?= $this->text('dashboard-translate-project-empty') ?></strong></p>
    <?php endif ?>

<?php
    $skip = $this->available;
    $skip[] = $this->project->lang;

    echo $this->insert('dashboard/partials/translate_menu', [
            'title' => $this->text('translator-add-translation'),
            'base_link' => '/dashboard/project/' .  $this->project->id . '/translate/',
            'languages' => $this->languages,
            'skip' => $skip]);
?>

<?php $this->replace() ?>
