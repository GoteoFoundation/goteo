<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php if(!empty($content->title)) { echo $content->title . ' - '; } ?><?php echo GOTEO_META_TITLE ?></title>
        <meta name="description" content="<?php echo GOTEO_META_DESCRIPTION ?>" />
        <meta name="keywords" content="<?php echo GOTEO_META_KEYWORDS ?>" />
        <meta name="author" content="<?php echo GOTEO_META_AUTHOR ?>" />
        <meta name="copyright" content="<?php echo GOTEO_META_COPYRIGHT ?>" />
        <meta name="robots" content="all" />
        <link rel="stylesheet" type="text/css" href="/view/css/goteo.css" />
        <?php if (!isset($useJQuery) || !empty($useJQuery)): ?>
        <script type="text/javascript" src="/view/js/jquery.js"></script>
        <?php endif ?>
    </head>

    <body<?php if (isset($bodyClass)) echo ' class="' . htmlspecialchars($bodyClass) . '"' ?>>

        <script type="text/javascript">
            // Mark DOM as javascript-enabled
            jQuery(document).bind('ready', function ($) { jQuery('body').addClass('js'); });
        </script>
        <noscript><!-- Please enable JavaScript --></noscript>

        <div id="wrapper">
