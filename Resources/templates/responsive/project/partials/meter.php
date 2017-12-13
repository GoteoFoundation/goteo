<?php

use Goteo\Library\Check;

    $project=$this->project;

    //Optimum and minimum size
    $minimum_size=@floor(($project->mincost * 85) / $project->maxcost);
    $optimum_size=85-$minimum_size;

    // For the separator between optimum + minimum
    $minimum_label=$minimum_size-4;

    // Minimum sizes
    $minimum_done=min($project->percent,100);
    $minimum_left=100-$minimum_done;

    // Optimum sizes
    $optimum_total_amount=$project->maxcost-$project->mincost;

    $optimum_reached_amount=$project->amount-$project->mincost;

    if($minimum_done>=100)
    {

        //The max is 100
        $optimum_done=min(@floor(($optimum_reached_amount / $optimum_total_amount) * 100),100);

        $optimum_left=100-$optimum_done;

    }

    // Over the optimum

    if($optimum_done>=100)
    {
        $extra_done=@floor(((($project->amount-$project->maxcost)*100))/($project->maxcost*0.5));

        // Max 100% (1.5 the optimum)
        $extra_done=min($extra_done, 100);
        $extra_left=100-$extra_done;

    }

    // Percentage marker

    // Having into account the margins (8%)

    if($minimum_done<100)
        $percentage_marker=@floor(($minimum_done*$minimum_size)/100)-8;
    elseif($optimum_done<100)
        $percentage_marker=$minimum_size+@floor(($optimum_done*$optimum_size)/100)-8;
    else
        $percentage_marker=min((85+@floor(($extra_done*20)/100)-8),92);


    //Day

    if ($project->inCampaign())
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

        $days_string='<br>'.date('d / m / Y', strtotime($date));
    }

?>

<div class="row hidden-xs">
    <div class="col-md-3 col-sm-4 thermometer-container">
        <div class="thermometer pull-left hidden-xs">
            <div class="extra">
                <div class="left <?= $extra_done!=100 ? 'top-border-radius' : '' ?>" style="height:<?= $extra_left.'%' ?>">
                </div>
                <div class="done <?= $extra_done==100 ? 'top-border-radius' : '' ?>" style="height:<?= $extra_done.'%' ?>">
                </div>
            </div>
            <div class="optimum" style="height:<?= $optimum_size.'%' ?>" >
                <div class="left" style="height:<?= $optimum_left.'%' ?>" >
                </div>
                <div class="done" style="height:<?= $optimum_done.'%' ?>" >
                </div>
            </div>
            <div class="minimum" style="height:<?= $minimum_size.'%' ?>" >
                <div class="left <?= !$minimum_done ? 'bottom-border-radius' : '' ?>" style="height:<?= $minimum_left.'%' ?>" >
                </div>
                <div class="done <?= $minimum_done ? 'bottom-border-radius' : '' ?>" style="height:<?= $minimum_done.'%' ?>" >
                </div>
            </div>
        </div>

        <div class="labels pull-left hidden-xs">
            <div class="minimum-label" style="bottom:<?= $minimum_label.'%' ?>">
            <img src="<?= SRC_URL . '/assets/img/project/arrow-meter.png' ?>"><span class="text">Min.</span>
            </div>
            <?php if($project->mincost!=$project->maxcost): ?>
                <div class="optimum-label">
                <img src="<?= SRC_URL . '/assets/img/project/arrow-meter.png' ?>"><span class="text">Opt.</span>
                </div>
            <?php endif; ?>
            <div class="percentage" style="bottom: <?= $percentage_marker.'%' ?>">
                <?= $project->percent.'%' ?>
            </div>
        </div>
    </div>
    <div class="col-md-9 col-sm-8 thermometer-info">
        <div class="time-status">
            <?php if (!empty($project->round)) : ?>
                <div class="round">
                    <?php if(!$project->one_round): ?>
                        <?= $project->round . $this->text('regular-round'); ?>
                    <?php else: ?>
                        <?= $this->text('regular-oneround_mark'); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="time-left">
            <?= $this->text($text).' '.$days_string ?>
            </div>
            <?php if($project->tagmark): ?>
                <div class="status">
                <?php   if($project->tagmark=='onrun-keepiton')
                            echo $this->text('regular-onrun_mark') . '<br />' . $this->text('regular-keepiton_mark');
                        else
                            echo $this->text('regular-'.$project->tagmark.'_mark')
                ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="reached-label">
        <?= $this->text('project-view-metter-got') ?>
        </div>
        <div class="reached">
        <?= amount_format($project->amount) ?>
        </div>
        <div class="optimum-label">
        <?= $this->text('project-view-metter-optimum') ?>
        </div>
        <div class="optimum">
        <?= amount_format($project->maxcost) ?>
        </div>
        <div class="minimum-label">
        <?= $this->text('project-view-metter-minimum') ?>
        </div>
        <div class="minimum">
        <?= amount_format($project->mincost) ?>
        </div>
    </div>
</div>

<?php if($project->inCampaign()): ?>
<div class="row hidden-xs" >
    <a href="/invest/<?= $project->id ?>" >
        <div class="col-lg-10 col-md-11 col-sm-12">
            <button class="btn btn-block pink custom col-sm-11 text-uppercase"><?= $this->text('regular-invest_it') ?></button>
        </div>
    </a>
</div>
<?php endif; ?>

<div class="row spacer-20 hidden-xs" id="bottom-meter">
    <div class="meter-investors">
        <?= $project->num_investors.' '.$this->text('project-view-metter-investors') ?>
    </div>

    <?php if(!$this->get_user() ): ?>
    <a href="/project/favourite/<?= $project->id ?>">
    <?php endif; ?>
        <div class="text-right favourite <?= $this->get_user()&&$this->get_user()->isFavouriteProject($project->id) ? 'active' : '' ?>" id="favourite">
            <span class="heart-icon glyphicon glyphicon-heart" aria-hidden="true"></span>
            <span> <?= $this->text('project-view-metter-favourite') ?></span>
        </div>
    <?php if(!$this->get_user() ): ?>
    </a>
    <?php endif; ?>

</div>
