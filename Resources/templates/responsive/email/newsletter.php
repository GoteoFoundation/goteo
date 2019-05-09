<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Goteo Newsletter</title>
    <style>
    @import url(https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic|Open+Sans+Condensed:300|Kalam);
    @media only screen and (max-width: 620px) {
            .wrapper .section {
                width: 100%;
            }
            .wrapper .column {
                width: 100%;
                display: block;
            }
            .pd-fundacion {
                padding: 20px 20px 0px 20px !important;
            }
            .pd-all {
            padding:  0 85% !important;
            }
            .pd-fundacion-dos {
                padding: 0px 50% 40px 40% !important;
            }
            .space-footer{
                display: none !important;
            }
            .img-center{
            display: inline-block;
            width: 45% !important;
            padding-left: 150px;
            margin-right: 50%;
            text-align: center;
            }
        .btn-proyectos{
            margin-left: 50% !important;
        }
        }
        @media only screen and (max-width: 320px) {
            .wrapper .section .full-img {
                width: 100%;
            }
            .wrapper .column {
                width: 100%;
                display: block;
            }
            .pd-fundacion {
            padding: 0px 0px 0px 0px !important;
            }
            .pd-all {
            padding:  0 85% !important;
            }
            .claim-fundacion{
                padding-left: 0px !important;
            }
            .pd-fundacion-dos {
            padding: 0px 10% 40px 25% !important;
            }
        }
        @media (min-width: 320px) and (max-width: 414px) {
            .claim-fundacion{
                padding: 20px 10px 0px 10px !important;
            }
        }
    </style>
</head>

<body>
    <table width="100%">
        <tbody>
            <tr>
                <td class="wrapper" width="600" align="center">
                 <!-- Top bar logo -->
                    <table class="section header" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#19b4b2">
                        <tr>
                            <td class="column" width="250" valign="top">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td align="left">
                                                <td> &nbsp; </td>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>

                            <td class="column" width="100" valign="top">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td align="left">
                                            	<img class="pd-all" src="<?= $this->get_url.'/assets/img/goteo-white.svg' ?>" alt="" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td class="column" width="200" valign="top">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td align="left">
                                                <td> &nbsp; </td>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>

                        </tr>
                    </table>
                    <!-- IMG HEADER -->
                    <table class="section header" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="column">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td align="left">
                                            	<img class="img-header" src="<?= $this->get_url.'/assets/img/newsletter/header.png' ?>" alt="Goteo" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Contenido -->
                    
					<?= $this->raw('content') ?>

                    <!-- BOTON VER PROYECTOS -->
                    <table class="section margin-btn" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="column" width="100%" valign="top">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td align="left">
                                                <p>
                                                	<a class="btn-proyectos" href="https://goteo.org/discover">VER MÁS PROYECTOS</a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>

	               <!-- FOOTER SOCIAL -->
                    <table class="section header" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#555555">
	                   <tr> 
                            <td align="center">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td style="padding-right: 10px; padding-top:20px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="https://t.me/goteofunding" target="_blank">
                                                    <img src="<?= $this->get_url.'/assets/img/newsletter/telegram.png' ?>" alt="Telegram" width="30" height="30" border="0" style="display: block;" />
                                                </a>
                                            </td>
                                            <td style="padding-right: 10px; padding-top:20px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="http://twitter.com/goteofunding" target="_blank">
                                                    <img src="<?= $this->get_url.'/assets/img/newsletter/twitter.png' ?>" alt="Twitter" width="30" height="30" border="0" style="display: block;" /></a>
                                            </td>
                                            <td style="padding-right: 10px; padding-top:20px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="https://www.facebook.com/goteofunding/" target="_blank">
                                                    <img src="<?= $this->get_url.'/assets/img/newsletter/facebook.png' ?>" alt="Facebook" width="30" height="30" border="0" style="display: block;" /></a>
                                            </td>
                                            <td style="padding-right: 10px; padding-top:20px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="https://instagram.com/goteofunding/" target="_blank">
                                                    <img src="<?= $this->get_url.'/assets/img/newsletter/instagram.png' ?>" alt="Instagram" width="30" height="30" border="0" style="display: block;" /></a>
                                            </td>
                                        </tr>
                                </td>
                            </tr>
	                    </table>

                    <!-- PRE-FOOTER -->
                    <table class="section footer header" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#555555">
                       <tr> 
                        <td align="center">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td style="padding-right: 10px; padding-top:20px;" colspan="0" align="center">
                                            <p>
                                                <a href="https://goteo.org/project/create"><?= $this->text('regular-create') ?></a>
                                            </p>
                                            <p>
                                                <?= $this->text('newsletter-block', $this->raw('unsubscribe')) ?>       
                                            </p>
                                        </td>
                                    </tr>
                        </tr>
                    </table>

 					<!-- PRE-FOOTER II -->
                    <table class="section footer" width="100%" cellpadding="0" cellspacing="0" bgcolor="#555555">
                        <tr>
                        	<td class="column" width="100" valign="top">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        	<td class="column" width="400" valign="top">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>
                                            	<p class="pd-description">
                                                 <?= $this->text('mailer-disclaimer') ?>   
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        	<td class="column" width="100" valign="top">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>

                   <!-- INICIATIVA GOTEO -->
                    <table class="section header" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#555555">
                       <tr> 
                            <td align="center">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td colspan="0" align="center">
                                                <p>
                                                <?= $this->text('footer-platoniq-iniciative') ?>
                                                </p>
                                            </td>
                                            <td style="padding-right: 10px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="http://fundacion.goteo.org/" target="_blank"><img src="<?= $this->get_url.'/assets/img/logo-fg-white.png' ?>" alt="Fundación Goteo" width="30" height="30" border="0" style="display: block;" /></a>
                                            </td>
                                        </tr>
                                </td>
                            </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <?php if ($this->tracker) : ?><img src="<?= $this->tracker ?>" alt="" /><?php endif ?>
</body>
</html>