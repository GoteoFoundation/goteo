<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project\Category,
    Goteo\Model\Invest,
    Goteo\Model\Image;

$URL = \SITE_URL;

$project = $vars['project'];
$level = $vars['level'] ?: 3;

if ($vars['global'] === true) {
    $blank = ' target="_blank"';
    $url = $URL;
} else {
    $blank = '';
    $url = '';
}

//si llega $vars['investor'] sacamos el total aportado para poner en "mi aporte"
if (isset($vars['investor']) && is_object($vars['investor'])) {
    $investor = $vars['investor'];
    $invest = Invest::supported($investor->id, $project->id);
    // si no ha aportado, que no ponga el avatar
    if (empty($invest->total)) unset($vars['investor']);
}

// veamos si tiene el grifo cerrado mientras continua en campaña
if ($project->status == 3 && $project->noinvest) {
    if($project->amount >= $project->mincost)
        $project->tagmark = 'gotit'; // banderolo financiado
    else
        $project->tagmark= '';
    $project->status = null; // para termometro, sin fecha de financiación
    $project->round = null; // no mostrar ronda
}
?>

<div class="widget project activable<?php if ($vars['balloon']) echo ' balloon' ?>">
	<a href="<?php echo $url ?>/project/<?php echo $project->id ?>" class="expand"<?php echo $blank; ?>></a>
    <?php if ($vars['balloon']): ?>
    <div class="balloon"><?php echo $vars['balloon'] ?></div>
    <?php endif ?>

    <div class="image">
        <?php switch ($project->tagmark) {
            case 'oneround': // "ronda única"
                break;
            case 'onrun': // "en marcha"
                echo '<div class="tagmark aqua">' . Text::get('regular-onrun_mark') . '</div>';
                break;
            case 'keepiton': // "aun puedes"
                echo '<div class="tagmark aqua">' . Text::get('regular-keepiton_mark') . '</div>';
                break;
            case 'onrun-keepiton': // "en marcha" y "aun puedes"
                  echo '<div class="tagmark aqua twolines"><span class="small"><strong>' . Text::get('regular-onrun_mark') . '</strong><br />' . Text::get('regular-keepiton_mark') . '</span></div>';
                break;
            case 'gotit': // "financiado"
                echo '<div class="tagmark violet">' . Text::get('regular-gotit_mark') . '</div>';
                break;
            case 'success': // "exitoso"
                echo '<div class="tagmark green">' . Text::get('regular-success_mark') . '</div>';
                break;
            case 'fail': // "caducado"
                echo '<div class="tagmark grey">' . Text::get('regular-fail_mark') . '</div>';
                break;
        } ?>

        <?php if (isset($vars['investor'])) : ?>
            <div class="investor"><img height="34" width="34" src="<?php echo $investor->avatar->getLink(34, 34, 1) ?>" alt="<?php echo $investor->name ?>" /><div class="invest"><?php echo Text::get('proj-widget-my_invest'); ?><br /><span class="amount"><?php echo \amount_format($invest->total); ?></span></div></div>
        <?php endif; ?>

        <?php if ($project->image instanceof Image): ?>
        <a href="<?php echo $url ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><img alt="<?php echo $project->id ?>" src="<?php echo $project->image->getLink(226, 130, true) ?>" /></a>
        <?php endif ?>
        <?php if (!empty($project->cat_names)): ?>
        <div class="categories">
        <?php $sep = ''; foreach ($project->cat_names as $key=>$value) :
            echo $sep.htmlspecialchars($value);
        $sep = ', '; endforeach; ?>
        </div>
        <?php endif ?>
    </div>

    <h<?php echo $level ?> class="title"><a href="<?php echo $url ?>/project/<?php echo $project->id ?>"<?php echo $blank; ?>><?php echo htmlspecialchars(Text::recorta($project->name,50)) ?></a></h<?php echo $level ?>>

    <h<?php echo $level + 1 ?> class="author"><?php echo Text::get('regular-by')?> <a href="<?php echo $url ?>/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"<?php echo $blank; ?>><?php echo htmlspecialchars(Text::recorta($project->user->name,37)) ?></a></h<?php echo $level + 1?>>

    <div class="description"><?php echo Text::recorta($project->description, 100); ?></div>

    <?php echo View::get('project/meter_hor.html.php', array('project' => $project)) ?>

    <div class="rewards">
        <h<?php echo $level + 1 ?>><?php echo Text::get('project-rewards-header'); ?></h<?php echo $level + 1?>>

        <ul>
           <?php if($project->rewards): foreach ($project->rewards as $reward): ?>
            <li class="<?php echo $reward->icon ?> activable">
                <?php $link_param= ($reward->type == 'individual') ? "/invest?amount=".\amount_format($reward->amount, 0, true) : "/rewards#social-rewards"; ?>
                <a href="<?php echo $url ?>/project/<?php echo $project->id.$link_param ?>" title="<?php echo htmlspecialchars("{$reward->icon_name}: {$reward->reward}"); if ($reward->type == 'individual') echo ' '.\amount_format($reward->amount); ?>" class="tipsy"<?php echo $blank; ?>><?php echo htmlspecialchars($reward->reward) ?></a>
            </li>
           <?php endforeach; endif ?>
        </ul>


    </div>

    <?php
    /*

    if ($_SESSION['assign_mode'] === true) : // boton para asignar ?>
    <div class="buttons" id="assign_<?php echo $project->id ?>">
        <?php if (!isset($_SESSION['call']->projects[$project->id])) : ?>
            <a class="button weak" href="#" onclick="return projAssign('<?php echo $project->id ?>');"><?php echo Text::get('regular-call-assign_this'); ?></a>
        <?php else : ?>
            <span style="color:red;"><?php echo Text::get('regular-call-assigned'); ?></span>
        <?php endif; ?>
    </div>
    <?php endif;

    */
    ?>


    <?php

    if ($_SESSION['user']->id == 'root') {
        echo "<!-- ".print_r($project, 1)." -->";
    }

    ?>
</div>
