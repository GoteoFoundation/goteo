<?php

$promote = $this->promote;
$project = $this->project;


$url = SITE_URL . '/project/' . $project->id;

?>

<?php
// Only first
if($this->key==0):

?>

<!-- FUNDACION TEST II -->

<table class="section header mt-40" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#ffec61" style="margin-top: 40px;">
    <tr>
        <td class="column" valign="top" style="max-width: 300px; margin: 0 auto;">
            <table style="margin: 0 auto;">
                <tbody>
                    <tr>
                        <td>
                            <h3 class="claim-fundacion" style="text-align: center;margin: 0; padding: 15px 0 15px 0;line-height: 1.6;">
                                <?= $this->text('newsletter-donate-description') ?>
                            </h3>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<!-- FUNDACION TEST -->

<table class="section header" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#ffec61">
    <tr>
        <td class="column" width="100" valign="top">
            <table style="margin: 0 auto;">
                <tbody>
                    <tr>
                        <td>
                            <a class="btn-fundacion" href="" style="color: #58595b;padding: 6px 12px;background-color: #fff;display: inline-block;margin-bottom: 20px;font-size: 14px;font-weight: 400;line-height: 1.42857143;text-align: center;white-space: nowrap;cursor: pointer;border: 1px solid transparent;border-radius: 4px;text-decoration: none; margin-top: 5px;"><?= $this->text('support-our-mission') ?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<!-- Projects title -->

<table class="section" cellpadding="0" cellspacing="0">
    <tr>
        <td class="column" width="100%" valign="top">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <h2 class="title-projects-section" style="margin: 0;padding: 0 20px 20px 20px;line-height: 1.6;font-size: 21px;color: #3a3a3a;text-align: center;margin-top: 35px;margin-bottom: 20px;">Algunos de nuestros Proyectos</h2>                        
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
                        <td align="left" class="project-container" style="background: #FAF8F8; height: 560px; padding:0; vertical-align: top; position: relative;">
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

                            <a style="padding: 10px 20px 20px 20px; font-size: 22px;line-height: 1;color: #2bbbb7 !important;font-weight: 400;display: block;" href="<?= $url ?>">
                            <span style="" class="font-weight: 500;">
                            <?= $this->ee($project->name) ?>
                            </span>
                            </a>
                            <h4 style="margin: 0;padding: 0 20px 20px 20px;line-height: 1.3;padding-bottom: 4px;font-size: 15px;font-weight: 400;">
                                <?= $this->text('regular-by') .' ' ?><span style="font-weight: 400; "><?= $project->user->name ?></span>
                            </h4>

                            <?php if($promote->getSocialCommitment()): ?>

                            <p style="margin: 0;padding: 10px 20px 20px 20px;line-height: 1.6;"> 
                                <span>
                                    <img src="<?= $promote->getSocialCommitment()->getIcon()->getLink(60, 60, false, true) ?>" style="max-width: 100%;display: inline-block;width: 8% !important; vertical-align: middle;">
                                <span style="padding-bottom: 4px;font-size: 13px;color: #919193;line-height: 0.8;"><?= $promote->getSocialCommitment()->name ?></span>
                            </span></p>
                            <?php endif; ?>

                            <p style="text-align: left;margin: 0;padding: 0 20px 20px 20px;line-height: 1.6;"><?= $project->subtitle ?></p>
                            <span class="progress-container" style="display:block; position: absolute; bottom: 13px;">
                                <p style="padding-bottom: 0px;margin: 0;padding: 0 20px 0 20px;line-height: 1.6;">
                                    <span style="font-size: 20px; font-weight: 500;">
                                       <?= \amount_format($project->amount) ?> 
                                    </span> <span style="color: #868788;"><?= $this->text('horizontal-project-reached') ?></span></p>
                                <hr style="width: 55%;margin-left: 20px;border: 1px solid #c6cdcc;">
                                <p style="margin: 0;padding: 0 20px 0 20px;line-height: 1.6;"><span style="font-size: 16px; font-weight: 500;"><?= $project->days.' '.strtolower($this->text('regular-days')) ?></span> <span style="color: #868788;"><?= $this->text('project-view-metter-days') ?></span></p>
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

<?php if($this->key%2!=0): ?>

    </tr>

</table>

<?php endif; ?>