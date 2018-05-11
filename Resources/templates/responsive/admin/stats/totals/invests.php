<?php

$this->layout('admin/stats/layout');

$filters = $this->a('filters');
$parts = ['raised', 'active', 'refunded', 'fees', 'commissions', 'matchfunding'];

$interval_active = $this->active ?: 'week';
$method_active = $this->methods ? key($this->methods) : 'global';
$intervals = $this->intervals;
// Add custom interval if a filter is defined
$intervals = ['custom' => $filters ? 'custom' : 'total'] + $intervals;
$interval_active = $this->active ?: 'custom';

?>

<?php $this->section('admin-stats-head') ?>

    <?= $this->insert('admin/partials/typeahead') ?>
    <?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters', ['hidden' => $filters + ['text' => $this->text]])) ?>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

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
        <?= $this->insertIf("admin/stats/totals/partials/invests/$part/scripts", ['target' => $part, 'intervals' => $intervals, 'query' => http_build_query($filters, '', '&')]) ?>
  <?php endforeach ?>

<?php $this->append() ?>
