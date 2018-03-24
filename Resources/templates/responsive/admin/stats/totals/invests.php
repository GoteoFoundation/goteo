<?php

$this->layout('admin/stats/layout');

// $query = http_build_query($this->filters);
$query = $value = '';
if($this->has_query('project')) {
    $value = $this->get_query('project');
    $query = http_build_query(['project' => $value]);
}
elseif($this->has_query('call')) {
    $value = $this->get_query('call');
    $query = http_build_query(['call' => $value]);
}
elseif($this->has_query('matcher')) {
    $value = $this->get_query('matcher');
    $query = http_build_query(['matcher' => $value]);
}
// Nice name
if($this->has_query('text')) $value = $this->get_query('text');

$parts = ['raised', 'active', 'refunded', 'fees', 'commissions'];

$interval_active = $this->active ?: 'week';
$method_active = $this->methods ? key($this->methods) : 'global';

?>

<?php $this->section('admin-container-body') ?>

<?= $this->insert('admin/partials/typeahead', ['value' => $value]) ?>
<?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters')) ?>

<ul class="nav nav-tabs" role="tablist">
  <?php foreach($parts as $part): ?>
    <li role="presentation"><a href="#<?= $part ?>" id="tab-menu-<?= $part ?>" data-target="#tab-<?= $part ?>" aria-controls="<?= $part ?>" role="tab" data-toggle="tab"><?= $this->text("admin-stats-$part") ?></a></li>
  <?php endforeach ?>
</ul>

<div class="tab-content">
  <?php foreach($parts as $part): ?>
    <div role="tabpanel" class="panel tab-pane" id="tab-<?= $part ?>">
        <?= $this->insertIf("admin/stats/totals/partials/invests/$part/menu") ?>
        <div class="panel-body"></div>
        <?= $this->insertIf("admin/stats/totals/partials/invests/$part/scripts", ['part' => $part]) ?>
    </div>
  <?php endforeach ?>
</div>

<?php $this->replace() ?>
