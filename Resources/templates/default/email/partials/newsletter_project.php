<?php

$promote = $this->promote;
$project = $this->project;


$url = SITE_URL . '/project/' . $project->id;

?>

<?php
// Only first
if($this->key==0):

?>

<div style="width: 600px; background-color: #ffffff;padding: 20px 10px 40px 20px;margin-top: 20px;">

    <h4 style="margin-top:0 !important;">
        <?= $this->text('newsletter-donate-description') ?>
    </h4>

    <a class="btn-support" style="background: #16A5A3; background-image: -webkit-linear-gradient(top, #16A5A3, #24837F); background-image: -moz-linear-gradient(top, #16A5A3, #24837F); background-image: -ms-linear-gradient(top, #16A5A3, #24837F); background-image: -o-linear-gradient(top, #16A5A3, #24837F); background-image: linear-gradient(to bottom, #16A5A3, #24837F); -webkit-border-radius: 11; -moz-border-radius: 11; border-radius: 6px; font-family: Arial; color: #ffffff; font-size: 15px; padding: 10px 20px 10px 20px; text-decoration: none; float: none !important; margin-top: 48px;" href="https://goteo.org/donate">
      <?= $this->text('support-our-mission') ?>
    </a>

</div>

<?php endif; ?>

<div style="width: 600px; background-color: #ffffff;padding: 20px 10px 20px 20px;margin-top: 20px;">

    <div>
        <a style="font-size:14px;font-weight:bold;text-transform:uppercase;text-decoration:none;color:#58595b;" href="<?= $url ?>"><?= $this->ee($project->name) ?></a>
    </div>
    <div style="vertical-align:top;padding-bottom:5px;padding-top:5px;">
        <a style="text-decoration:none;color: #929292;font-size:12px;" href="<?= $url ?>"><?= $this->text('regular-by').' '.$project->user->name ?></a>
    </div>

    <div style="width: 226px; padding-bottom:10px;">
        <?php if ($project->image):

            $url_imagen = $project->image->getLink(255, 130, true);
            if (strpos($url_imagen, '//') === 0) {
                $url_imagen = 'http://'.substr($url_imagen, 2);
            }
            ?>
        <a href="<?= $url ?>"><img alt="<?= $this->ee($project->name) ?>" src="<?= $url_imagen ?>" width="255" height="130" /></a>
        <?php endif ?>
    </div>

    <div style="font-size: 12px;text-transform: uppercase; padding-bottom:10px; padding-top:10px; color: #38b5b1;"><?= $this->text('project-view-categories-title') ?>: <?php $sep = ''; foreach ($project->cat_names as $key=>$value) {echo $sep.htmlspecialchars($value); $sep = ', '; } ?></div>

    <div style="width:600px;vertical-align:top;border-right:2px solid #f1f1f1;line-height:15px;padding-right:10px;">
        <a style="text-decoration:none;font-size:14px;color: #797979;" href="<?= $url ?>"><?= $project->subtitle ?></a>
    </div>

    <div style="width: 25px;height: 2px;border-bottom: 1px solid #38b5b1;margin-bottom: 10px; margin-top:10px;"></div>

    <div style="font-size: 14px;vertical-align: top;text-transform: uppercase; padding-bottom:10px;"><?= $this->text('project-view-metter-investment') ?>: <span style="font-size:14px;color:#96238F;font-weight: bold;"><?= $this->text('project-view-metter-minimum') . ' ' . \amount_format($project->mincost) ?></span>  <span style="color:#FFF;">_</span>  <span style="font-size:14px;color:#ba6fb6;font-weight: bold;"><?= $this->text('project-view-metter-optimum') . ' ' . \amount_format($project->maxcost) ?></span>
    </div>
    <span style="font-size: 14px;line-height: 14px; padding-top:10px; padding-bottom:10px; margin-bottom:10px;text-transform: uppercase;"><?= $this->text('project-view-metter-days') ?>: <strong style="text-transform: none;"><?= $project->days.' '. $this->text('regular-days') ?></strong></span>
</div>
