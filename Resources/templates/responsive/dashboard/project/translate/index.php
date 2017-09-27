<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-tabs') ?>
<?php $this->replace() ?>

<?php $this->section('dashboard-translate-project') ?>

    <?php if($this->translated): ?>
        <p><?= $this->text('dashboard-translate-project-currents', [
                '%LANG%' => '<strong><em>' . $this->languages[$this->project->lang] . '</em></strong>',
                '%NUM%' => '<strong>' . count($this->translated) . '</strong>'
                ]) ?></p>
        <div class="list-group">
        <?php foreach($this->translated as $lang):
            $percent1 = $this->project->getLangsPercent($lang);
            $cost = current($this->project->costs);
            $percent2 = $cost ? $cost->getLangsGroupPercent($lang, ['project']) : 0;
        ?>
                <a class="list-group-item" href="/dashboard/project/<?= $this->project->id ?>/translate/overview/<?= $lang ?>">
                    <i class="icon icon-edit" title="<?= $this->text('regular-edit') ?>"></i>
                    <?= $this->languages[$lang] ?>
                    <?= $this->percent_badge($percent1, $this->text('translator-percent', $percent1)) ?>
                    <?= $this->percent_badge($percent2, $this->text('translator-percent', $percent2)) ?>
                </a>
        <?php endforeach ?>
        </div>
    <?php else: ?>
        <p class="text-danger"><strong><?= $this->text('dashboard-translate-project-empty') ?></strong></p>
    <?php endif ?>

<?php
    $skip = $this->translated;
    $skip[] = $this->project->lang;

    echo $this->insert('dashboard/partials/translate_menu', [
            'default_title' => $this->text('translator-add-translation'),
            'base_link' => '/dashboard/project/' .  $this->project->id . '/translate/overview/',
            'languages' => $this->languages,
            'lang' => null,
            'skip' => $skip]);
?>

<?php $this->replace() ?>
