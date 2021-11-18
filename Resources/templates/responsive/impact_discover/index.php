<?php $this->layout('impact_discover/layout') ?>

<?php $this->section('impact-discover-content') ?>

<?= $this->insert('impact_discover/partials/filters') ?>

<?= $this->insert('impact_discover/partials/sdg_list', ['sdgSelected' => $this->sdgSelected]) ?>

<?= $this->insert('impact_discover/partials/list_projects') ?>

<?php
    $projects = is_array($this->projects)? $this->projects : [];
?>

<div class="spacer-20 spacer-bottom text-center more-projects-button <?= $this->total > count($projects) ? '' : ' hidden' ?>">
    <button class="btn btn-link"><?= $this->text('regular-load-more') ?> &nbsp; &nbsp; <i class="fa fa-chevron-right"></i></button>
</div>

<?php $this->replace() ?>
