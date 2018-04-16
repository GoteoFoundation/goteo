<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->text('dashboard-menu-activity-summary') ?></h1>
    <h5><?= $this->project->name ?></h5>

    <div class="panel section-content">
        <div class="panel-body">
            <h3><?= $this->text('form-project-status-title') ?></h3>

            <ol class="breadcrumb">
            <?php foreach ($this->statuses as $i => $s): ?>
                <?php if ($i == $this->project->status): ?>
                    <li class="active"><?= $s ?></li>
                <?php else: ?>
                    <li><?= $s ?></li>
                <?php endif ?>
            <?php endforeach ?>
            </ol>

            <?= $this->supply('matcher-section', $this->insert('dashboard/project/partials/matcher_section')) ?>

            <?= $this->insert('dashboard/project/partials/summary_status', ['project' => $this->project]) ?>

            <?php if($this->project->inEdition()): ?>
                <?= $this->insert('project/widgets/validation', ['init_percent' => 0, 'validation' => $this->validation]) ?>
            <?php endif ?>

        </div>
    </div>

    <div class="panel section-content">
        <div class="panel-body">
            <h3><?= $this->text('project-progress-title') ?></h3>
            <ul class="data-list">
                <li>
                    <h5><?= $this->text('project-obtained') ?></h5>
                    <p><?= amount_format($this->project->invested) ?></p>
                </li>
                <li class="divider"></li>
                <li>
                    <h5><?= $this->text('project-menu-supporters') ?></h5>
                    <p><?= $this->project->num_investors ?></p>
                </li>
                <li class="divider"></li>
                <li>
                    <h5><?= $this->text('project-collaborations-number') ?></h5>
                    <p><?= $this->project->num_messengers ?></p>
                </li>
            </ul>

            <div class="chart-amount"></div>
        </div>
    </div>

    <?php $url = $this->get_url() . '/widget/project/' . $this->project->id; ?>

    <div class="panel section-content">
        <div class="panel-heading">
            <h3><?= $this->text('project-spread-widget_title') ?></h3>
        </div>
        <div class="panel-body widget-preview">
            <div class="right">
                <?php if(!$this->project->isApproved()): ?>
                    <div class="alert alert-orange"><i class="fa fa-exclamation-triangle"></i> <?= $this->text('project-widget-not-visible') ?></div>
                <?php endif ?>
                <h5 onclick="$(this).next().focus();$(this).next().select()"><?= $this->text('project-spread-embed_code') ?></h5>
                <textarea class="form-control" onclick="this.focus();this.select()" rows="4" readonly="readonly"><?= $this->text_widget($url) ?></textarea>
            </div>
            <div class="left">
                <iframe frameborder="0" height="492px" src="<?= $url ?>" width="300px" scrolling="no"></iframe>
            </div>
        </div>
    </div>

    <?php if($this->project->userCanDelete($this->get_user())): ?>
    <div class="panel section-content">
        <h3><?= $this->text('dashboard-project-delete') ?></h3>
        <a class="btn btn-danger" href="/dashboard/project/<?= $this->project->id ?>/delete" onclick="return confirm('<?= $this->ee($this->text('dashboard-project-delete_alert'), 'js') ?>')"><i class="fa fa-trash"></i> <?= $this->text('regular-delete') ?></a>
    </div>
    <?php endif ?>

  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?php if($this->project->isApproved()): ?>
    <?= $this->insert('project/partials/chart_amount.php', ['project' => $this->project]) ?>
<?php endif ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    var $c100 = $('.c100');
    var per = 0;
    var percent = <?= (int)$this->validation->global ?>;
    $c100.removeClass('p0');
    (function animateCircle() {
        $c100.removeClass('p' + per);
        per++;
        $c100.addClass('p' + per);
        $c100.contents('span').text(per + '%');
        if(per < percent) {
            setTimeout(animateCircle, Math.ceil(per/5));
        }
    })();
});

// @license-end
</script>

<?php $this->append() ?>
