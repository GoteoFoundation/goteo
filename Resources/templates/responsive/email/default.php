<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name=”x-apple-disable-message-reformatting”>
    <title><?= $this->subject ? $this->subject : 'Goteo Mailer'  ?></title>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic|Open+Sans+Condensed:300|Kalam);
        
        body {
            -webkit-text-size-adjust: none;
            text-size-adjust: none;
        }
        a{
            text-decoration: none;
            color: #337ab7;
        }

        h1,h2,h3,h4,h5,h6,p {
            margin: 0;
            padding: 0 20px 20px 20px;
            line-height: 1.6;
        }

        h1{
            font-size: 28px;
            color: #149290;
            text-align: center;
        }
        h2{
            font-size: 18px;
            color: #a63c98;
            text-align: center;
        }

        .footer p a{
            color: #FFF;
            text-align: center;
            padding-bottom: 0px;
            font-weight: 400;
            text-decoration: underline;
        }

        .footer p{
            color: #FFF;
            text-align: center;
            padding-bottom: 0px;
            font-weight: 200;
            font-weight: 400;
        }
        
        li{
            margin-bottom: 10px;
        }
        
        .text-center{
            text-align: center;
        }
        img {
            max-width: 100%;
        }
        @media only screen and (max-width: 620px) {
            .wrapper .section {
                width: 100%;
            }
            .wrapper .column {
                width: 100% !important;
                display: block;
                max-width: 100% !important;
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
            .project-container{
                height: auto !important
            }
            .progress-container{
                position: inherit !important;
                padding: 20px 0 10px 0; 
            }
        }
        @media only screen and (max-width: 320px) {
            .wrapper .section .full-img {
                width: 100% !important;
                display: block;
                max-width: 100% !important;
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
        @media(min-width: 320px) and (max-width: 414px) {
            .claim-fundacion{
                padding: 20px 10px 0px 10px !important;
            }
        }
    </style>
</head>

<body style="padding: 0; margin: 0; border: none; border-spacing: 0px; border-collapse: collapse; vertical-align: top; font-family: Roboto,sans-serif; color: #3a3a3a;">
    <table style=" width: 100%; padding: 0; margin: 0; border: none; border-spacing: 0px; border-collapse: collapse;vertical-align: top;">
        <tbody style="padding: 0; margin: 0; border: none; border-spacing: 0px; border-collapse: collapse;vertical-align: top;">
            <tr>
                <td class="wrapper" align="center" style="padding: 0; margin: 0; border: none; border-spacing: 0px; border-collapse: collapse;vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; max-width: 600px !important;">
                 <!-- Top bar logo -->
                    <table class="section header" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#19b4b2" style="padding: 0; margin: 0; border: none; border-spacing: 0px; border-collapse: collapse;vertical-align: top;">
                        <tr>
                            <td class="column" valign="top" style="padding: 0; margin: 0; border: none; border-spacing: 0px; border-collapse: collapse;vertical-align: top;">
                                <table style="padding: 0; margin: 0; border: none; border-spacing: 0px; border-collapse: collapse;vertical-align: top; margin: 0 auto;">
                                    <tbody>
                                        <tr>
                                            <td align="left" style="padding: 0; margin: 0; border: none; border-spacing: 0px; border-collapse: collapse;vertical-align: top;">
                                                <img width="100%" src="<?= $this->asset('img/newsletter/goteo-white.png') ?>" alt="" style="padding: 15px 0; margin: 0; border: none; border-spacing: 0px; border-collapse: collapse;vertical-align: top;" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Contenido -->

                    <div style="font-size: 16px; margin: 30px 0;">
                    <table class="section header" cellpadding="0" cellspacing="0" width="600">
                        <tr>
                            <td class="column">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td align="left"> <?= ($this->type == 'md')? $this->markdown($this->content) : $this->raw('content') ?> </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table> 
                        
                    <?php if ($this->promotes): ?>
                    
                        <!-- PROMOTED PROJECTS -->
                        <?php foreach($this->promotes as $key => $promote) : ?>
                        
                        <?= $this->insert('email/partials/newsletter_project', ['project'=>$promote, 'key' => $key, 'total' => count($this->promotes), 'lang' => $this->lang]); ?>
                        
                        <?php endforeach ?>

                    <?php endif ?>

                    </div>


                    <?php if ($this->promotes): ?>
                        
                        <!-- BOTON VER PROYECTOS -->
                        <table class="section" style="margin-top: 40px; margin-bottom: 80px; margin-right: 22px;" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="column" width="100%" valign="top">
                                    <table align="center">
                                        <tbody>
                                            <tr>
                                                <td align="left">
                                                    <p>
                                                        <a style="color: #ffffff; padding: 13px 0; background-color: #19b4b2; display: inline-block; padding: 6px 12px; margin-bottom: 0;font-size: 14px;font-weight: 400; line-height: 1.42857143; text-align: center; white-space: nowrap; cursor: pointer; border: 1px solid transparent; border-radius: 4px; text-decoration: none;" href="https://goteo.org/discover">
                                                            <?= $this->t('mailer-more-projects-button', $this->lang) ?>    
                                                            </a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>

                    <?php endif ?>

                   <!-- FOOTER SOCIAL -->
                    <table class="section header" cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#555555">
                       <tr> 
                            <td align="center">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td style="padding-right: 10px; padding-top:20px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="https://t.me/goteofunding" target="_blank">
                                                    <img src="<?= $this->asset('img/newsletter/telegram.png') ?>" alt="Telegram" width="30" height="30" border="0" style="display: block;" />
                                                </a>
                                            </td>
                                            <td style="padding-right: 10px; padding-top:20px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="http://twitter.com/goteofunding" target="_blank">
                                                    <img src="<?= $this->asset('img/newsletter/twitter.png') ?>" alt="Twitter" width="30" height="30" border="0" style="display: block;" /></a>
                                            </td>
                                            <td style="padding-right: 10px; padding-top:20px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="https://www.facebook.com/goteofunding/" target="_blank">
                                                    <img src="<?= $this->asset('img/newsletter/facebook.png') ?>" alt="Facebook" width="30" height="30" border="0" style="display: block;" /></a>
                                            </td>
                                            <td style="padding-right: 10px; padding-top:20px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="https://instagram.com/goteofunding/" target="_blank">
                                                    <img src="<?= $this->asset('img/newsletter/instagram.png') ?>" alt="Instagram" width="30" height="30" border="0" style="display: block;" /></a>
                                            </td>
                                        </tr>
                                </td>
                            </tr>
                        </table>


                    <!-- PRE-FOOTER II -->
                    <table class="section footer" cellpadding="0" width="100%" cellspacing="0" bgcolor="#555555">
                        <tr>
                            <td class="column" align="center" valign="top" style="max-width: 900px; padding: 20px 0; display: block; margin: 0 auto;">
                                <p class="pd-description">
                                 <?= $this->t('mailer-disclaimer', $this->lang) ?>   
                                </p>                                    
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
                                                <p style="color: #FFF;">
                                                <?= $this->t('footer-platoniq-iniciative', $this->lang) ?>
                                                </p>
                                            </td>
                                            <td style="padding-right: 10px;" colspan="0" align="center">
                                                <a style="text-decoration: none; color: #212121;" href="http://fundacion.goteo.org/" target="_blank"><img src="<?= $this->asset('img/logo-fg-white.png') ?>" alt="Fundación Goteo" height="30" border="0" style="display: block;" /></a>
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
