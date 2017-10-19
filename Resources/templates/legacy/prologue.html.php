<?php

use Goteo\Library\Text,
    Goteo\Application\Lang,
    Goteo\Application\Config;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?= Lang::current() ?>">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo GOTEO_META_TITLE ?></title>
        <link rel="icon" type="image/png" href="/myicon.png" />
        <meta name="description" content="<?php  echo Text::get('meta-description-index');?>" />
        <meta name="keywords" content="<?php echo GOTEO_META_KEYWORDS ?>" />
        <meta name="author" content="<?php echo GOTEO_META_AUTHOR ?>" />
        <meta name="copyright" content="<?php echo GOTEO_META_COPYRIGHT ?>" />
        <meta name="robots" content="all" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<?php if (isset($ogmeta)) : ?>
        <meta property="og:title" content="<?php echo $ogmeta['title'] ?>" />
        <meta property="og:type" content="activity" />
        <meta property="og:site_name" content="Goteo.org" />
        <meta property="og:description" content="<?php echo $ogmeta['description'] ?>" />
        <?php if (is_array($ogmeta['image'])) :
            foreach ($ogmeta['image'] as $ogimg) : ?>
        <meta property="og:image" content="<?php echo $ogimg ?>" />
        <?php endforeach;
        else : ?>
        <meta property="og:image" content="<?php echo $ogmeta['image'] ?>" />
        <?php endif; ?>
        <meta property="og:url" content="<?php echo $ogmeta['url'] ?>" />
<?php else : ?>
        <meta property="og:title" content="Goteo.org" />
        <meta property="og:description" content="<?php echo GOTEO_META_DESCRIPTION ?>" />
        <meta property="og:image" content="<?php echo SRC_URL ?>/goteo_logo.png" />
        <meta property="og:url" content="<?php echo SITE_URL ?>" />
<?php endif; ?>

<?php

// Incluir la nueva plantilla compilada (del nuevo sistema de vistas, no hay problema, es simple php)
// Version "dist"

if(is_file(GOTEO_WEB_PATH . 'templates/partials/header/styles.php')) {
    include GOTEO_WEB_PATH . 'templates/partials/header/styles.php';
}
else {
    include GOTEO_PATH . 'public/templates/default/partials/header/styles.php';
}
 ?>

        <script type="text/javascript">
        // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
                if(navigator.userAgent.indexOf('Mac') != -1)
        {
            document.write ('<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/mac.css" />');
        }
        // @license-end
	    </script>

        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.tipsy.min.js"></script>
        <!-- custom scrollbars -->
        <link type="text/css" href="<?php echo SRC_URL ?>/view/css/jquery.jscrollpane.min.css" rel="stylesheet" media="all" />
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.mousewheel.min.js"></script>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.jscrollpane.min.js"></script>
        <!-- end custom scrollbars -->

        <!-- sliders -->
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.slides.min.js"></script>
        <!-- end sliders -->

        <!-- fancybox-->
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.fancybox.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/fancybox/jquery.fancybox.min.css" media="screen" />
        <!-- end custom fancybox-->

        <?php if (isset($jscrypt)) : ?>
            <script src="<?php echo SRC_URL ?>/view/js/sha1.min.js"></script>
        <?php endif; ?>

        <?php if (isset($superform)) : ?>
            <script src="<?php echo SRC_URL ?>/view/js/datepicker.min.js"></script>
            <script src="<?php echo SRC_URL ?>/view/js/datepicker/datepicker.<?php echo LANG; ?>.js"></script>
            <script src="<?php echo SRC_URL ?>/view/js/superform.js"></script>
        <?php endif; ?>

        <?php if (isset($jsreq_autocomplete)) : ?>
            <link href="<?php echo SRC_URL ?>/view/css/jquery-ui-1.10.3.autocomplete.min.css" rel="stylesheet" />
            <script src="<?php echo SRC_URL ?>/view/js/jquery-ui-1.10.3.autocomplete.min.js"></script>
        <?php endif; ?>
        <?php if (isset($jsreq_ckeditor)) : ?>
           <script type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/ckeditor/ckeditor.js"></script>
        <?php endif; ?>

<?php
    // Place for custom header styles/scripts for legacy views
    if(Config::get('alternate.legacy.header')) {
        include Config::get('alternate.legacy.header');
    }
?>

    </head>

    <body<?php if (isset($bodyClass)) echo ' class="' . htmlspecialchars($bodyClass) . '"' ?>>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    // Mark DOM as javascript-enabled
    jQuery(document).ready(function ($) {
        $('body').addClass('js');
        $('.tipsy').tipsy();
        /* Rolover sobre los cuadros de color */
        $("li").hover(
                function () { $(this).addClass('active') },
                function () { $(this).removeClass('active') }
        );
        $('.activable').hover(
            function () { $(this).addClass('active') },
            function () { $(this).removeClass('active') }
        );
        $(".a-null").click(function (event) {
            event.preventDefault();
        });
    });
// @license-end
</script>

<div id="wrapper">
