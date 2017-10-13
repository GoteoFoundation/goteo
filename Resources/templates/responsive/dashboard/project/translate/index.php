<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-tabs') ?>
<?php $this->replace() ?>

<?php $this->section('dashboard-translate-project') ?>

    <?php if($this->translated): ?>
        <p><?= $this->text('dashboard-translate-project-currents', [
                '%LANG%' => '<strong><em>' . $this->languages[$this->project->lang] . '</em></strong>',
                '%NUM%' => '<strong>' . count($this->translated) . '</strong>'
                ]) ?></p>

        <!-- <div class="list-group"> -->
        <?php foreach($this->translated as $lang):
            $percent1 = $this->project->getLangsPercent($lang);
            $cost = current($this->project->costs);
            $percent2 = $cost ? $cost->getLangsGroupPercent($lang, ['project']) : 0;
            $reward = current($this->project->rewards);
            $percent3 = $reward ? $reward->getLangsGroupPercent($lang, ['project']) : 0;
        ?>
                <blockquote>
                    <h4><?= $this->languages[$lang] ?>
                      <span class="btn-group pull-right">
                        <a class="btn btn-default btn-lg" title="<?= $this->text('translator-percent', $percent1) ?>" href="/dashboard/project/<?= $this->project->id ?>/translate/overview/<?= $lang ?>">
                            <strong><?= $this->text('step-main') ?></strong> <?= $this->percent_badge($percent1) ?>
                        </a>
                        <a class="btn btn-default btn-lg" title="<?= $this->text('translator-percent', $percent2) ?>" href="/dashboard/project/<?= $this->project->id ?>/translate/costs/<?= $lang ?>">
                            <?= $this->text('step-4') ?> <?= $this->percent_badge($percent2) ?>
                        </a>
                        <a class="btn btn-default btn-lg" title="<?= $this->text('translator-percent', $percent3) ?>" href="/dashboard/project/<?= $this->project->id ?>/translate/rewards/<?= $lang ?>">
                            <?= $this->text('step-5') ?> <?= $this->percent_badge($percent3) ?>
                        </a>
                      </span>
                    </h4>
                </blockquote>
        <?php endforeach ?>
        <!-- </div> -->
    <?php else: ?>
        <blockquote><?= $this->text('dashboard-translate-project-empty') ?></blockquote>
    <?php endif ?>

<?php
    $skip = $this->translated;
    $skip[] = $this->project->lang;

    echo $this->insert('dashboard/partials/translate_menu', [
            'default_title' => $this->text('translator-add-translation'),
            'base_link' => '/dashboard/project/' .  $this->project->id . '/translate/overview/',
            'languages' => $this->languages,
            'lang' => null,
            'btn_class' => 'btn-cyan btn-lg',
            'skip' => $skip]);
?>

<?php $this->replace() ?>
