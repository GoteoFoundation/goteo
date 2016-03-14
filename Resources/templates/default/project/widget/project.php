<?php

use Goteo\Core\View,
    Goteo\Model\Project\Category,
    Goteo\Model\Invest,
    Goteo\Model\Image;

$URL = \SITE_URL;

$project = $this->project;
$level = $this->level ?: 3;


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

<div class="widget project activable<?php if ($this->balloon) echo ' balloon' ?>">
	<a href="/project/<?php echo $project->id ?>" class="expand"></a>
    <?php if ($this->balloon): ?>
    <div class="balloon"><?php echo $this->balloon ?></div>
    <?php endif ?>

    <div class="image">
        <?php switch ($project->tagmark) {
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
        } ?>

        <?php if (isset($this->investor)) : ?>
            <div class="investor"><img src="<?php echo $investor->avatar->getLink(34, 34, 1) ?>" alt="<?php echo $investor->name ?>" /><div class="invest"><?php echo $this->text('proj-widget-my_invest'); ?><br /><span class="amount"><?php echo \amount_format($invest->total); ?></span></div></div>
        <?php endif; ?>

        <?php if ($project->image instanceof Image): ?>
        <a href="/project/<?php echo $project->id ?>"><img alt="<?php echo $project->id ?>" src="<?php echo $project->image->getLink(226, 130, true) ?>" /></a>
        <?php endif ?>
        <?php if (!empty($project->cat_names)): ?>
        <div class="categories">
        <?php $sep = ''; foreach ($project->cat_names as $key=>$value) :
            echo $sep.htmlspecialchars($value);
        $sep = ', '; endforeach; ?>
        </div>
        <?php endif ?>
    </div>

    <h<?php echo $level ?> class="title"><a href="/project/<?php echo $project->id ?>"><?php echo htmlspecialchars($this->text_truncate($project->name,50)) ?></a></h<?php echo $level ?>>

    <h<?php echo $level + 1 ?> class="author"><?php echo $this->text('regular-by')?> <a href="/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"><?php echo htmlspecialchars($this->text_truncate($project->user->name,37)) ?></a></h<?php echo $level + 1?>>

    <div class="description"><?php echo $this->text_truncate($project->description, 100); ?></div>

    <?php echo View::get('project/meter_hor.html.php', array('project' => $project)) ?>

    <div class="rewards">
        <h<?php echo $level + 1 ?>><?php echo $this->text('project-rewards-header'); ?></h<?php echo $level + 1?>>

        <ul>
           <?php foreach ($project->rewards as $reward): ?>
            <li class="<?php echo $reward->icon ?> activable">
                <?php $link_param= ($reward->type == 'individual') ? "/invest?amount=".\amount_format($reward->amount, 0, true) : "/rewards#social-rewards"; ?>
                <a href="/project/<?php echo $project->id.$link_param ?>" title="<?php echo htmlspecialchars("{$reward->icon_name}: {$reward->reward}"); if ($reward->type == 'individual') echo ' '.\amount_format($reward->amount); ?>" class="tipsy"><?php echo htmlspecialchars($reward->reward) ?></a>
            </li>
           <?php endforeach ?>
        </ul>


    </div>

</div>
