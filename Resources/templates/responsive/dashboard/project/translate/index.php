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
        <?php
        $zones = $this->a('zones');
        $percents = $this->a('percents');
        foreach($this->translated as $lang):
        ?>
            <blockquote>
                <h4><i class="fa fa-globe"></i> <?= $this->languages[$lang] ?></h4>
                <div class="btn-group">
                  <?php foreach($zones as $zone => $name): ?>
                    <a class="btn btn-default btn-lg" title="<?= $this->text('translator-percent', $percent1) ?>" href="/dashboard/project/<?= $this->project->id ?>/translate/<?= $zone ?>/<?= $lang ?>">
                      <strong><?= $name ?></strong> <?= $this->percent_badge($percents[$lang][$zone]) ?>
                    </a>
                  <?php endforeach ?>
                </div>
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
