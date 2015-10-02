<?php

use Goteo\Core\View,
    Goteo\Model\Project\Category,
    Goteo\Model\Invest,
    Goteo\Model\Image,
    Goteo\Library\Check;

$URL = \SITE_URL;

$project = $this->project;
$level = $this->level ?: 3;

$date_created = $project->created;
$date_updated = $project->updated;
$date_success = $project->success;
$date_published = $project->published;

$date_closed  = $project->closed;

$days       = $project->days;
$days_round1 = $project->days_round1;
$days_total = $project->days_total;
$round      = $project->round;
$status     = $project->status;
$status=$project->status;

$reached= $project->invested;
$minimum= $project->mincost;

$minimum_done_per = floor(($reached / $minimum) * 100);


if ($this->global === true) {
    $blank = ' target="_blank"';
    $url = $URL;
} else {
    $blank = '';
    $url = '';
}

//si llega $this->investor sacamos el total aportado para poner en "mi aporte"
if (isset($this->investor) && is_object($this->investor)) {
    $investor = $this->investor;
    $invest = Invest::supported($investor->id, $project->id);
    // si no ha aportado, que no ponga el avatar
    if (empty($invest->total)) unset($this->investor);
}

// veamos si tiene el grifo cerrado mientras continua en campaña
if ($project->status == 3 && $project->noinvest) {
    $project->tagmark = 'gotit'; // banderolo financiado
    $project->status = null; // para termometro, sin fecha de financiación
    $project->round = null; // no mostrar ronda
}


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
$text=strtolower($this->text('project-view-metter-days'));

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
    $text=strtolower($this->text($text));
    $days_left=date('d/m/Y', strtotime($date));
}

?>

<div class="widget project horizontal">
	<a href="<?php echo $url ?>/project/<?php echo $project->id ?>" class="expand"<?php echo $blank; ?>></a>

    <h<?php echo $level ?> class="title horizontal"><a href="<?php echo $url ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><?= htmlspecialchars($project->name,150) ?></a></h<?php echo $level ?>>
    <h<?php echo $level + 1 ?> class="author"><?php echo $this->text('regular-by')?> <a href="<?php echo $url ?>/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"<?php echo $blank; ?>><?php echo htmlspecialchars($this->text_truncate($project->user->name,37)) ?></a></h<?php echo $level + 1?>>

    <div class="image">
        <?php if ($project->image instanceof Image): ?>
        <a href="<?php echo $url ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><img alt="<?php echo $project->id ?>" src="<?php echo $project->image->getLink(672, 250, true) ?>" /></a>
        <?php endif ?>
        <?php if (!empty($project->cat_names)): ?>
        <div class="categories">
        <?php $sep = ''; foreach ($project->cat_names as $key=>$value) :
            echo $sep.htmlspecialchars($value);
        $sep = ', '; endforeach; ?>
        </div>
        <?php endif ?>
    </div>


    <div class="description">
        <?php echo $this->text_truncate($project->description, 225); ?>
        <ul class="amounts">
            <li>
                <div class="amount"><?= \amount_format($reached) ?></div>
                <div class="label"><?= $this->text('horizontal-project-reached') ?></div>
            </li>
            <li>
                <div class="amount"><?= $minimum_done_per.' %' ?></div>
                <div class="label"><?= $this->text('horizontal-project-percent') ?></div>
            </li>
             <li>
                <div class="amount"><?= $project->num_investors ?></div>
                <div class="label"><?= strtolower($this->text('project-view-metter-investors')) ?></div>
            </li>
            <li>
                <div class="amount"><?= $days_left ?></div>
                <div class="label"><?= $text ?></div>
            </li>
        </ul>
    </div>

</div>
