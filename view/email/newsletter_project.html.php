<?php
use Goteo\Library\Text,
    Goteo\Model\Project\Category,
    Goteo\Model\Image;

$promote = $this['promote'];
$project = $this['project'];


$categories = Category::getNames($project->id, 2);

// retornos
$icons = array();
$q = 1;
foreach ($project->social_rewards as $social) {
    $icons[] = $social->icon;
    if ($q >= 5) break; $q++;
}
if ($q < 5) foreach ($project->individual_rewards as $individual) {
    $icons[] = $individual->icon;
    if ($q >= 5) break; $q++;
}


?>
<div class="project">

    <div class="project-header">
        <?php echo htmlspecialchars($promote->title); ?>
        <span class="project-header-desc">
            <p><?php echo $promote->description; ?></p>
        </span>
    </div>

    <div class="line">
    </div>

    <div class="project-tit">
        <a href="<?php echo SITE_URL.'/project/'.$project->id ?>"><?php echo htmlspecialchars($project->name) ?></a>
    </div>
    <div class="project-autor">
        <?php echo Text::get('regular-by').' '.htmlspecialchars($project->user->name) ?>
    </div>
    <table width="646" border="0" cellpadding="5">
        <tr>
            <td rowspan="5" class="project-img">
                <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
                <img alt="<?php echo $project->name ?>" src="<?php echo str_replace('beta.goteo.org', 'goteo.org', current($project->gallery)->getLink(255, 130, true)) ?>" width="255" height="130" />
                <?php endif ?>
            </td>
            <td rowspan="6" class="project-txt"><?php echo Text::recorta($project->description, 100); ?></td>
            <td colspan="2" class="camp-txt"><?php echo Text::get('project-view-metter-investment'); ?></td>
        </tr>
        <tr>
            <td class="project-min"><?php echo Text::get('project-view-metter-minimum'); ?></td>
            <td class="project-opt"><?php echo Text::get('project-view-metter-optimum'); ?></td>
        </tr>
        <tr>
            <td class="project-valor1"><?php echo \amount_format($project->mincost) ?><img src="http://www.goteo.org/view/css/euro/violet/s.png" width="19" height="16" /></td>
            <td class="project-valor2"><?php echo \amount_format($project->maxcost) ?><img src="http://www.goteo.org/view/css/euro/light-violet/s.png" width="19" height="16" /></td>
        </tr>
        <tr>
            <td colspan="2" class="camp-txt"><?php echo Text::get('project-rewards-header'); ?></td>
        </tr>
        <tr>
            <td colspan="2" class="project-categoria">
                <?php foreach ($icons as $icon) : ?>
                <img src="http://www.goteo.org/view/css/icon/s/<?php echo $icon ?>.png" width="22" height="22" alt="<?php echo $icon ?>"/>
               <?php endforeach ?>
            </td>
        </tr>
        <tr>
            <td class="project-categoria">
                <?php $sep = ''; foreach ($categories as $key=>$value) :
                    echo $sep.htmlspecialchars($value);
                $sep = ', '; endforeach; ?>
            </td>
            <td colspan="2" class="project-quedan"<?php echo Text::get('project-view-metter-days'); ?> <span class="project-dias"><?php echo $project->days.' '.Text::get('regular-days'); ?></span></td>
        </tr>
    </table>
</div>
