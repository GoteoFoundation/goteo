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

<table class="section header mt-40" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#ffec61">
    <tr>
        <td class="column" width="150" valign="top">
            <table>
                <tbody>
                    <tr>
                        <td> &nbsp; </td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td class="column" width="300" valign="top">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <h3 class="claim-fundacion" style="text-align: center">
                                <?= $this->text('newsletter-donate-description') ?>
                            </h3>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td class="column" width="150" valign="top">
            <table>
                <tbody>
                    <tr>
                        <td> &nbsp; </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<!-- FUNDACION TEST -->

<table class="section header" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#ffec61">
    <tr>
        <td class="column" width="420" valign="top">
            <table>
                <tbody>
                    <tr>
                        <td> &nbsp; </td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td class="column" width="100" valign="top">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <p class="pd-fundacion-dos">
                                <a class="btn-fundacion" href=""><?= $this->text('support-our-mission') ?></a>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td class="column" width="200" valign="top">
            <table>
                <tbody>
                    <tr>
                        <td> &nbsp; </td>
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
                            <h2 class="title-algunos">Algunos de nuestros Proyectos</h2>                        
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<?php endif; ?>


<?php if($this->key%2==0): ?>

<table class="section proyectos" cellpadding="0" cellspacing="0">

    <tr>

<?php endif; ?>

        <td class="column" width="290" valign="top">
            <table bgcolor="#FFFFFF">
                <tbody>
                    <tr>
                        <td align="left">
                             <?php if ($project->image):

                                $url_imagen = $project->image->getLink(255, 130, true);
                                if (strpos($url_imagen, '//') === 0) {
                                    $url_imagen = 'http://'.substr($url_imagen, 2);
                                }
                                ?>
                            <a href="<?= $url ?>"><img alt="<?= $this->ee($project->name) ?>" src="<?= $url_imagen ?>" width="300" height="130" /></a>

                            <?php endif ?>

                            <h3 class="title-projects"><?= $this->ee($project->name) ?></h3>
                            <h4 class="subtitle-projects"><?= $this->text('regular-by').' '.$project->user->name ?></h4>
                            <p> <span><img class="icons" src="img/crear-cultura.png" alt=""/></span> <span class="icon-info">Crea Cultura</span></p>
                            <p style="text-align:left;"><?= $project->subtitle ?></p>
                            <p style="padding-bottom: 0px;">
                                <span style="font-size: 20px; font-weight: 500;">
                                <?= \amount_format($project->amount) ?> 
                                </span> <span style="color: #868788;">Conseguido</span></p>
                            <hr />
                            <p><span style="font-size: 16px; font-weight: 500;"><?= $project->days.' '.$this->text('regular-days') ?></span> <span style="color: #868788;">faltan</span></p>
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