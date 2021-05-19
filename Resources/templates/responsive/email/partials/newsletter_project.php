<?php

$project = $this->project;


$url = SITE_URL . '/project/' . $project->id;

?>

<?php
// Only first
if($this->key==0):

?>

<!-- Projects title -->

<table class="section" cellpadding="0" cellspacing="0">
    <tr>
        <td class="column" width="100%" valign="top">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <h2 class="title-projects-section" style="margin: 0;padding: 0 20px 20px 20px;line-height: 1.6;font-size: 21px;color: #3a3a3a;text-align: center;margin-top: 35px;margin-bottom: 20px;">
                                <?= $this->t('home-projects-title', $this->lang) ?>
                            </h2>                        
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<?php endif; ?>


<?php if($this->key%2==0): ?>

<table class="section proyectos" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">

    <tr>

<?php endif; ?>

        <td class="column" valign="top" style="max-width: 300px; ">
            <table bgcolor="#FFFFFF">
                <tbody>
                    <tr>
                        <td align="left" class="project-container" style="background: #FAF8F8; height: 580px; padding:0; vertical-align: top; position: relative;">
                             <?php if ($project->image):

                                $url_imagen = $project->image->getLink(500, 300, true);
                                if (strpos($url_imagen, '//') === 0) {
                                    $url_imagen = 'http://'.substr($url_imagen, 2);
                                }
                                ?>
                            <a href="<?= $url ?>">
                                <img alt="<?= $this->ee($project->name) ?>" src="<?= $url_imagen ?>" style="width: 100%;display: block; height: auto">
                            </a>

                            <?php endif ?>

                            <a style="padding: 10px 20px 10px 20px; font-size: 22px;line-height: 1;color: #2bbbb7 !important;font-weight: 400;display: block;" href="<?= $url ?>">
                            <span style="" class="font-weight: 500;">
                            <?= $this->ee($project->name) ?>
                            </span>
                            </a>
                            <h4 style="margin: 0;padding: 0 20px 20px 20px;line-height: 1.3;padding-bottom: 4px;font-size: 15px;font-weight: 400;">
                                <?= $this->t('regular-by', $this->lang) .' ' ?><span style="font-weight: 400; "><?= $this->ee($project->user->name) ?></span>
                            </h4>

                            <?php if($project->getSocialCommitment()): ?>

                            <p style="margin: 0;padding: 10px 20px 10px 20px;line-height: 1.6;"> 
                                <span>
                                    <img src="<?= $project->getSocialCommitment()->getIcon()->getLink(60, 60, false, true) ?>" style="max-width: 100%;display: inline-block;width: 8% !important; vertical-align: middle;">
                                <span style="padding-bottom: 4px;font-size: 13px;color: #919193;line-height: 0.8;"><?= $project->getSocialCommitment()->name ?></span>
                            </span></p>
                            <?php endif; ?>

                            <p style="text-align: left;margin: 0;padding: 0 20px 20px 20px;line-height: 1.6;"><?= $project->subtitle ?></p>
                            <span class="progress-container" style="display:block; position: absolute; bottom: 13px;">
                                <p style="padding-bottom: 0px;margin: 0;padding: 0 20px 0 20px;line-height: 1.6;">
                                    <span style="font-size: 20px; font-weight: 500;">
                                       <?= \amount_format($project->amount) ?> 
                                    </span> <span style="color: #868788;"><?= $this->t('horizontal-project-reached', $this->lang) ?></span></p>
                                <hr style="width: 55%;margin-left: 20px;border: 1px solid #c6cdcc; font-size: 7px;">
                                <p style="margin: 0;padding: 0 20px 0 20px;line-height: 1.6;"><span style="font-size: 16px; font-weight: 500;"><?= $project->days.' '.strtolower($this->t('regular-days', $this->lang)) ?></span> <span style="color: #868788;"><?= $this->t('project-view-metter-days', $this->lang) ?></span></p>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>

        <?php if($this->key%2==0): ?>

        <td class="column" width="20" valign="top">
            <table>
                <tbody>
                    <tr>
                        <td> &nbsp; </td>
                    </tr>
                </tbody>
            </table>
        </td>

    <?php endif; ?>

<?php if($this->key%2!=0 || ($this->total-1) == $this->key): ?>

    </tr>

</table>

<?php endif; ?>