<?php

use Goteo\Core\View,
    Goteo\Model\Project\Category,
    Goteo\Model\Invest,
    Goteo\Model\Image;

$URL = \SITE_URL;



$project = $this->project;
$level = $this->level ?: 3;

$date_created = $project->created;
$date_updated = $project->updated;
$date_success = $project->success;
$date_closed  = $project->closed;
$days       = $project->days;

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
?>

<div class="widget project horizontal">
	<a href="<?php echo $url ?>/project/<?php echo $project->id ?>" class="expand"<?php echo $blank; ?>></a>
    
    <h<?php echo $level ?> class="title horizontal"><a href="<?php echo $url ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><?= htmlspecialchars($project->name,150) ?></a></h<?php echo $level ?>>
    <h<?php echo $level + 1 ?> class="author"><?php echo $this->text('regular-by')?> <a href="<?php echo $url ?>/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"<?php echo $blank; ?>><?php echo htmlspecialchars($this->text_recorta($project->user->name,37)) ?></a></h<?php echo $level + 1?>>

    <div class="image">
        <?php /*switch ($project->tagmark) {
            case 'oneround': // "ronda única"
                break;
            case 'onrun': // "en marcha"
                echo '<div class="tagmark aqua">' . $this->text('regular-onrun_mark') . '</div>';
                break;
            case 'keepiton': // "aun puedes"
                echo '<div class="tagmark aqua">' . $this->text('regular-keepiton_mark') . '</div>';
                break;
            case 'onrun-keepiton': // "en marcha" y "aun puedes"
                  echo '<div class="tagmark aqua twolines"><span class="small"><strong>' . $this->text('regular-onrun_mark') . '</strong><br />' . $this->text('regular-keepiton_mark') . '</span></div>';
                break;
            case 'gotit': // "financiado"
                echo '<div class="tagmark violet">' . $this->text('regular-gotit_mark') . '</div>';
                break;
            case 'success': // "exitoso"
                echo '<div class="tagmark green">' . $this->text('regular-success_mark') . '</div>';
                break;
            case 'fail': // "caducado"
                echo '<div class="tagmark grey">' . $this->text('regular-fail_mark') . '</div>';
                break;
        }*/ ?>

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
        <?php echo $this->text_recorta($project->description, 225); ?>
        <div style="border-top:1px solid #1AB3B1; width:10%; margin-top:5px; display:none;"></div>
        <ul class="amounts">
            <li>
                <div class="amount"><?= \amount_format($reached) ?></div>
                <div class="label">conseguidos</div>
            </li>
            <li>
                <div class="amount"><?= $minimum_done_per.' %' ?></div>
                <div class="label">financiado</div>
            </li>
            <li>
                <div class="amount"><?= $project->num_investors ?></div>
                <div class="label">cofinanciadores</div>
            </li>
        </ul>
    </div>

</div>
