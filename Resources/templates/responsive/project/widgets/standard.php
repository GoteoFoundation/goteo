<?php
use Goteo\Library\Check;

$project = $this->project;

$percentage= $this->project->mincost ? floor(($this->project->amount/$this->project->mincost)*100) : 0;
$status = $project->status;

$date_created = $project->created;
$date_updated = $project->updated;
$date_success = $project->success;
$date_published = $project->published;

$date_closed = $project->closed;

$days       = $project->days;
$days_round1 = $project->days_round1;
$days_total = $project->days_total;
$round      = $project->round;

if ($status == 3)
{ // en campaña
    if ($days > 2) {
        $days_left = number_format($days);
        $days_left2 = $this->text('regular-days');
    } else {

        $part = strtotime($date_published);
        if ($round == 1) {
            $plus = $days_round1;
        }
        elseif ($round == 2) {

        $plus = $days_total;
        $final_day = date('Y-m-d', mktime(0, 0, 0, date('m', $part), date('d', $part)+$plus, date('Y', $part)));
        $days_left = Check::time_togo($final_day, 1);
        $days_left2 = '';
        }
    }
$days_left.=' '.$this->text('regular-days');
$text = strtolower($this->text('project-view-metter-days'));

}

elseif (!empty($status)) {
    switch ($status) {
        case 1: // en edicion
          $text = 'project-view-metter-day_created';
            $date = $date_created;

           case 2: // pendiente de valoración
            $text = 'project-view-metter-day_updated';
            $date = $date_updated;
            break;

        case 4: // financiado
        case 5: // retorno cumplido
            $text = 'project-view-metter-day_success';
            $date = $date_success;
            break;

        case 6: // archivado
            $text = 'project-view-metter-day_closed';
           $date = $date_closed;
            break;
    }
    $text = strtolower($this->text($text));
    $days_left = date('d/m/Y', strtotime($date));
}

?>
<div class="project-widget standard" id="project-<?= $this->project->id ?>">
    <?php if(!empty($this->project->image)): ?>
    <a class="img-link" href="/project/<?= $this->project->id ?>">
        <img class="img-responsive img-project" src="<?= $this->project->image->getLink(600, 250, true); ?>">
        <?php if($this->project->called): ?>
        <div class="call-mark">
            <img class="img-responsive" src="<?= '/assets/img/project/drop.svg' ?>" >
        </div>
        <?php endif; ?>
    </a>
    <?php endif; ?>
    <div class="content <?= $project->called ? 'called': '' ?>  ">
        <div class="title"><a href="/project/<?= $this->project->id ?>"><?= $this->text_truncate($this->project->name, 80); ?></a></div>
        <div class="author">
            <a href="/user/profile/<?= $this->project->user->id?>" style="color:#20B3B2 !important" target="_blank"><?= $this->text('regular-by').' '.$this->project->user->name ?></a>
        </div>
        <div class="description">
            <?= $this->text_truncate($this->project->subtitle, 140) ?>
        </div>

        <?php if($project->called): ?>
        <?php //amout depending on the call configurating
            if (!empty($project->called->rawmaxproj))
                $call_amount_rest = $project->called->rawmaxproj;
            elseif(!empty($project->called->maxproj))
                $call_amount_rest = $project->called->maxproj;
            else
                $call_amount_rest = $project->called->rest;

            $project_drop_rest = $call_amount_rest-$project->amount_call;
        ?>
        <div class="call-amount">

            <?php if($project->status==3&&$project->round==1): ?>
            <div>
                <?= $this->text('call-project-widget-remain', amount_format($project_drop_rest)) ?>
            </div>
            <?php endif; ?>

            <div>
                <?= $this->text('call-project-got_explain', amount_format($project->amount_call)) ?>
            </div>

        </div>
        <?php endif; ?>

    </div>
    <ul class="amounts list-unstyled text-center">
            <li class="col-xs-4">
                <div class="amount"><?= amount_format($this->project->invested) ?></div>
                <div class="data-label"><?= $this->text('horizontal-project-reached') ?></div>
            </li>
            <li class="col-xs-4">
                <div class="amount"><?= $percentage.' %' ?></div>
                <div class="data-label"><?= $this->text('horizontal-project-percent') ?></div>
            </li>
            <li class="col-xs-4">
                <div class="amount"><?= $days_left ?></div>
                <div class="data-label"><?= $text ?></div>
            </li>
    </ul>


    <?php if($this->admin): ?>
    <div class="project-widget-admin">
        <div class="project-widget-admin-panel">
            <div class="btn-group">
                <a class="btn btn-success" href="/dashboard/project/<?= $project->id ?>/summary"><i class="fa fa-eye"></i> <?= $this->text('dashboard-menu-activity-summary') ?></a>
                <a class="btn btn-success" href="/project/<?= $project->id ?>" target="_blank"><i class="fa fa-external-link"></i> <?= $this->text('regular-preview') ?></a>
            </div>
        </div>
        <div class="project-widget-admin-panel">
            <div class="btn-group">
                <a class="btn btn-pink" href="/project/edit/<?= $project->id ?>"><i class="fa fa-edit"></i> <?= $this->text('regular-edit') ?></a>
            </div>
        </div>
        <div class="project-widget-admin-panel">
            <div class="btn-group">
                <a class="btn btn-pink" href="/dashboard/project/<?= $project->id ?>/images"><i class="fa fa-image"></i> <?= $this->text('images-main-header') ?></a>
            </div>
        </div>
        <div class="project-widget-admin-panel">
            <div class="btn-group">
                <a class="btn btn-pink" href="/dashboard/project/<?= $project->id ?>/analytics"><i class="fa fa-pie-chart"></i> <?= $this->text('profile-fields-analytics-title') ?></a>
                <a class="btn btn-pink" href="/dashboard/project/<?= $project->id ?>/materials"><i class="fa fa-beer"></i> <?= $this->text('project-share-materials') ?></a>
            </div>
        </div>
    </div>
    <?php endif ?>

</div>

