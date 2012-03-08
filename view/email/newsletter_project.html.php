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

<div style="background-color: #ffffff;padding: 20px 10px 10px 20px;margin-top: 20px;">

    <div style="color: #38b5b1;font-weight: bold;text-transform: uppercase;">
        <strong><?php echo htmlspecialchars($promote->title); ?></strong><br />
        <span style="font-size: 14px;font-weight: normal;font-style: normal;text-transform: capitalize;"><?php echo $promote->description; ?></span>
    </div>

    <div style="width: 25px;height: 2px;border-bottom: 1px solid #38b5b1;margin-bottom: 15px;"></div>

    <div style="font-size: 14px;font-weight: bold;padding-left: 5px;">
        <a style="text-decoration: none;color: #58595b;" href="<?php echo SITE_URL.'/project/'.$project->id ?>"><?php echo htmlspecialchars($project->name) ?></a>
    </div>
    <div style="font-size: 11px;color: #434343;vertical-align: top;padding-bottom: 5px;padding-top: 5px;color: #929292;padding: 5px;">
        <?php echo Text::get('regular-by').' '.htmlspecialchars($project->user->name) ?>
    </div>
    <div style="font-size: 14px;color: #797979;vertical-align: top;border-right: 2px solid #f1f1f1;line-height: 15px;padding-right: 10px;"><?php echo Text::recorta($project->description, 300); ?></div>
    <div style="width: 226px;">
        <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
        <a href="<?php echo SITE_URL.'/project/'.$project->id ?>"><img alt="<?php echo $project->name ?>" src="<?php echo str_replace('beta.goteo.org', 'goteo.org', current($project->gallery)->getLink(255, 130, true)) ?>" width="255" height="130" /></a>
        <?php endif ?>
    </div>
    <div style="font-size: 10px;color: #434343;text-transform: uppercase;">Categor&iacute;as: <?php $sep = ''; foreach ($categories as $key=>$value) {echo $sep.htmlspecialchars($value); $sep = ', '; } ?></div>
    <div style="font-size: 12px;vertical-align: top;text-transform: uppercase;"><?php echo Text::get('project-view-metter-investment'); ?>:&nbsp;
        <span style="font-size:12px;color:#96238F;"><?php echo Text::get('project-view-metter-minimum') . ' ' . \amount_format($project->mincost) . '&euro;'; ?>&nbsp;</span>
        &nbsp;&nbsp;&nbsp;
        <span style="font-size:12px;color:#ba6fb6;"><?php echo Text::get('project-view-metter-optimum') . ' ' . \amount_format($project->maxcost) . '&euro;'; ?>&nbsp;</span>
    </div>
    <!--
    <div style="font-size: 10px;color: #434343;text-transform: uppercase;"><?php echo Text::get('project-rewards-header'); ?>&nbsp;
    <?php foreach ($icons as $icon) : ?><img src="http://www.goteo.org/view/css/icon/s/<?php echo $icon ?>.png" width="22" height="22" alt="<?php echo $icon ?>"/><?php endforeach ?>
    </div>
    -->
    <div style="font-size: 11px; line-height: 14px;"<?php echo Text::get('project-view-metter-days'); ?> <span style="font-weight: bold;font-size: 14px;line-height: 14px;"><?php echo $project->days.' '.Text::get('regular-days'); ?></span></div>
</div>
