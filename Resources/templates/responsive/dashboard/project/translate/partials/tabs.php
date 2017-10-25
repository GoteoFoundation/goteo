<?php
$zones = $this->a('zones');
$percents = $this->a('percents');
if(!$zones) return;
?>
<ul class="nav nav-tabs nav-justified">

<?php foreach($zones as $zone => $name): ?>
  <li role="tab" <?= $this->step == $zone ? ' class="active"' : '' ?>><a href="/dashboard/project/<?= $this->project->id ?>/translate/<?= $zone ?>/<?= $this->lang ?>"><?= $name ?><?= $this->percent_badge($percents[$zone])  ?></a></li>
<?php endforeach ?>

</ul>

<blockquote class="padding-right">
    <?= $this->text('dashboard-translate-project-translating', ['%LANG%' => '<strong><em>' . $this->languages[$this->lang] . '</em></strong>', '%ORIGINAL%' => '<strong><em>' . $this->languages[$this->project->lang] . '</em></strong>']) ?>

    <?= $this->insert('dashboard/partials/translate_menu', [
        'base_link' => '/dashboard/project/' .  $this->project->id . '/translate/' . $this->step . '/',
        'languages' => $this->languages,
        'translated' => $this->translated,
        'lang' => $this->lang,
        'class' => 'pull-right',
        'skip' => [$this->project->lang],
        'exit_link' => '/dashboard/project/' .  $this->project->id . '/translate'
    ]) ?>
</blockquote>
