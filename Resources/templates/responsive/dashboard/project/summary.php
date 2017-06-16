<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

    <h1><?= $this->text('dashboard-menu-activity-summary') ?></h1>
    <h1><?= $this->project->name ?></h1>

    <div class="panel panel-default section-content">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $this->text('form-project-status-title') ?></h3>
        </div>
        <div class="panel-body">

            <ol class="breadcrumb">
            <?php foreach ($this->statuses as $i => $s): ?>
                <?php if ($i == $this->project->status): ?>
                    <li class="active"><span><?= $s ?></span></li>
                <?php else: ?>
                    <li><a><?= $s ?></a></li>
                <?php endif ?>
            <?php endforeach ?>
            </ol>

            <?php if ($this->status_text): ?>
                <div class="alert alert-danger"><?= $this->status_text ?></div>
            <?php endif ?>

        </div>
    </div>

    <div class="panel panel-default section-content">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?= $this->project->num_investors.' '.$this->text('project-menu-supporters') ?>
            </h3>
            <h3 class="panel-title" >
                <?= $this->project->num_messengers.' '.$this->text('project-collaborations-number') ?>
            </h3>
        </div>

        <div class="panel-body">
            <div class="chart-amount text-center"></div>
        </div>
    </div>

    <?php $url = $this->get_url() . '/widget/project/' . $this->project->id; ?>

    <div class="panel panel-default section-content">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $this->text('project-spread-widget_title') ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <iframe frameborder="0" height="480px" src="<?= $url ?>" width="250px" scrolling="no"></iframe>
                </div>
                <div class="col-md-6 col-xs-12">
                <div class="wc-embed" onclick="$('#code').focus();$('#code').select()"><?= $this->text('project-spread-embed_code') ?></div>
                <textarea class="form-control" onclick="this.focus();this.select()" readonly="readonly"><?= $this->text_widget($url) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default section-content">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $this->text('dashboard-project-delete') ?></h3>
        </div>
        <div class="panel-body">
            <a class="btn btn-danger" href="/project/delete/<?= $this->project->id ?>" onclick="return confirm('<?= $this->ee($this->text('dashboard-project-delete_alert'), 'js') ?>')"><i class="fa fa-trash"></i> <?= $this->text('regular-delete') ?></a>
        </div>
    </div>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?= $this->insert('project/partials/chart_amount.php', ['project' => $this->project]) ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){

})

// @license-end
</script>

<?php $this->append() ?>
