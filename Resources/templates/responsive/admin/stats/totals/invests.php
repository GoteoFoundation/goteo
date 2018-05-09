<?php

$this->layout('admin/stats/layout');

// $query = http_build_query($this->filters);
$query = $filters = [];
$value = '';
if($this->has_query('project')) {
    $value = $this->get_query('project');
    $query['project'] = $value;
}
elseif($this->has_query('call')) {
    $value = $this->get_query('call');
    $query['call'] = $value;
}
elseif($this->has_query('channel')) {
    $value = $this->get_query('channel');
    $query['channel'] = $value;
}
elseif($this->has_query('matcher')) {
    $value = $this->get_query('matcher');
    $query['matcher'] = $value;
}
elseif($this->has_query('user')) {
    $value = $this->get_query('user');
    $query['user'] = $value;
}
// Nice name
if($this->has_query('text')) $value = $this->get_query('text');

if($this->has_query('from')) {
    $filters['from'] = $this->get_query('from');
}
if($this->has_query('to')) {
    $filters['to'] = $this->get_query('to');
}
$parts = ['raised', 'active', 'refunded', 'fees', 'commissions', 'matchfunding'];

$interval_active = $this->active ?: 'week';
$method_active = $this->methods ? key($this->methods) : 'global';
$intervals = $this->intervals;
// Add custom interval if a filter is defined
if($query || $filters) {
    $intervals = ['custom' => $filters ? 'custom' : 'total'] + $intervals;
    $interval_active = $this->active ?: 'custom';
}
?>

<?php $this->section('admin-container-body') ?>

<?= $this->insert('admin/partials/typeahead', ['value' => $value]) ?>
<?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters', ['hidden' => $query + ['text' => $value]])) ?>

<div class="stats-invests">
    <ul class="nav nav-tabs" role="tablist">
      <?php foreach($parts as $part): ?>
        <li role="presentation"><a href="#<?= $part ?>" id="tab-menu-<?= $part ?>" data-target="#tab-<?= $part ?>" aria-controls="<?= $part ?>" data-toggle="tab" role="tab"><?= $this->text("admin-stats-$part") ?></a></li>
      <?php endforeach ?>
    </ul>

    <div class="tab-content">
      <?php foreach($parts as $part): ?>
        <div role="tabpanel" class="panel tab-pane" id="tab-<?= $part ?>">
            <div class="panel-body">
                <?= $this->insertIf("admin/stats/totals/partials/invests/$part/menu",  ['intervals' => $intervals]) ?>
                <div class="stats-charts"></div>
            </div>
        </div>
      <?php endforeach ?>
    </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

  <?php foreach($parts as $part): ?>
        <?= $this->insertIf("admin/stats/totals/partials/invests/$part/scripts", ['target' => $part, 'intervals' => $intervals, 'query' => http_build_query($query + $filters)]) ?>
  <?php endforeach ?>

<?php $this->append() ?>
