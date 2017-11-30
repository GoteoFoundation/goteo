<?php

use Goteo\Library\Check;

    $project=$this->project;

    //Day

    if ($project->status == 3)
    { // en campaña

        if ($project->days > 2)
        {
            $days_left = number_format($project->days);
            $days_left2 = $this->text('regular-days');
        }
        else
        {
            $part = strtotime($project->published);

            if ($project->round == 1)
            {
                $plus = $project->days_round1;
            }
            elseif ($project->round == 2)
            {
                $plus = $project->days_total;
            }


            $final_day = date('Y-m-d', mktime(0, 0, 0, date('m', $part), date('d', $part)+$plus, date('Y', $part)));
            $days_left = Check::time_togo($final_day, 1);
            $days_left2 = '';
        }

        $text='project-view-metter-days';
        $days_string=$days_left.' '.$days_left2;

    }

    elseif (!empty($project->status)) {
        switch ($project->status) {
            case 1: // en edicion
                $text = 'project-view-metter-day_created';
                $date = $project->created;
                break;

            case 2: // pendiente de valoración
                $text = 'project-view-metter-day_updated';
                $date = $project->updated;
                break;

            case 4: // financiado
            case 5: // retorno cumplido
                $text = 'project-view-metter-day_success';
                $date = $project->success;
                break;

            case 6: // archivado
                $text = 'project-view-metter-day_closed';
                $date = $project->closed;
                break;
            }

        $days_string=' '.date('d / m / Y', strtotime($date));

    }

?>

<?php if($project->status==3): ?>

<div class="row visible-xs" >
    <a href="/invest/<?= $project->id ?>" >
        <button class="btn btn-block pink custom col-md-11 text-uppercase"><?= $this->text('regular-invest_it') ?></button>
    </a>
</div>

<?php endif; ?>

<div class="row responsive-meter visible-xs">
        <div class="round-left-time">
            <?php if(!empty($project->round)): ?>
                <span class="round">
                <?php if(!$project->one_round): ?>
                <?= $project->round . $this->text('regular-round'); ?>
                <?php else: ?>
                <?= $this->text('regular-oneround_mark'); ?>
                <?php endif; ?>
                </span>
            <?php endif; ?>
            <span>
                <?= $this->text($text).' '.$days_string ?>
            </span>
        </div>


        <div class="status">
            <?php if($project->tagmark): ?>
            <?php   if($project->tagmark=='onrun-keepiton')
                        echo $this->text('regular-onrun_mark').' '.$this->text('regular-keepiton_mark');
                    else
                        echo $this->text('regular-'.$project->tagmark.'_mark')
            ?>
            <?php endif; ?>
        </div>

        <div class="row meter-numbers">
            <div class="item reached-container">
                <div class="meter-label">
                <?= $this->text('project-view-metter-got') ?>
                </div>
                <div class="reached">
                <?= amount_format($project->amount) ?>
                </div>
            </div>
            <div class="item">
                <div class="meter-label">
                <?= $this->text('project-view-metter-minimum') ?>
                </div>
                <div class="opt-min">
                <?= amount_format($project->mincost) ?>
                </div>
            </div>
            <div class="item">
                <div class="meter-label">
                <?= $this->text('project-view-metter-optimum') ?>
                </div>
                <div class="opt-min">
                <?= amount_format($project->maxcost) ?>
                </div>
            </div>
        </div>

</div>

<div class="meter-investors visible-xs">
    <?= $project->num_investors.' '.$this->text('project-view-metter-investors') ?>
</div>

<?php if($project->called): ?>

<a href="<?php echo SITE_URL ?>/call/<?php echo $project->called->id ?>/projects" target="_blank">
    <div class="row call-info visible-xs">
        <div class="col-xs-2 no-padding" >
            <img src="<?= SRC_URL . '/assets/img/project/drop.svg' ?>" class="img-responsive">
        </div>
        <div class="col-xs-10 info-default-call" >
            <div class="header-text"><?= $project->called->user->name.' '.$this->text('call-project-get') ?></div>
            <div class="call-name">
                <?= $project->called->name ?>
            </div>
        </div>
        <div class="col-xs-10 info-hover-call display-none" >
            <div class="header-text"><?= $project->called->user->name.' '.$this->text('call-project-get') ?></div>
                <div class="call-name">
                <?= $this->text('project-call-got', amount_format($project->amount_call), $project->called->user->name) ?>
                </div>
        </div>
    </div>
</a>

<?php endif; ?>

 <?php if ($project->node !== $this->get_config('current_node')&&($project->nodeData->active)) : ?>

    <div class="visible-xs channel" >
        <span class="channel-label">
            <img src="/assets/img/project/channel.svg" width="20"> <?= $this->text('regular-channel') ?> 
        </span>
        <a href="<?= $project->nodeData->url ?>">
            <button class="btn" style="<?= $project->nodeData->owner_background ? 'background-color: '.$project->nodeData->owner_background :  '' ?>" > 
                <?= $project->nodeData->name ?>    
            </button>
        </a>
    </div>

<?php endif; ?>



<div class="row visible-xs extra-responsive-meter">
    <?php if(!$this->get_user() ): ?>
        <a href="/project/favourite/<?= $project->id ?>">
    <?php endif; ?>
        <div class="favourite <?= $this->get_user()&&$this->get_user()->isFavouriteProject($project->id) ? 'active' : '' ?>" id="favourite">
            <button class="btn btn-block favourite <?= $this->get_user()&&$this->get_user()->isFavouriteProject($project->id) ? 'active' : '' ?>">
                <span class="heart-icon glyphicon glyphicon-heart" aria-hidden="true"></span>
                <span> <?= $this->text('project-view-metter-favourite') ?></span>
            </button>
        </div>
    <?php if(!$this->get_user() ): ?>
        </a>
    <?php endif; ?>
</div>
