<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
    <?php $this->section('head') ?>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?=$this->title?></title>
        <link rel="icon" type="image/png" href="/myicon.png" />

        <?=$this->insert("partials/header/metas")?>

        <?=$this->insert("partials/header/styles")?>

        <!-- processhtml:remove:dist -->
        <script src="//localhost:35729/livereload.js"></script>
        <!-- /processhtml -->


        <!--[if IE]>
        <link href="<?php echo SRC_URL ?>/view/css/ie.css" media="screen" rel="stylesheet" type="text/css" />
        <![endif]-->

        <script type="text/javascript">
        if(navigator.userAgent.indexOf('Mac') != -1)
        {
            document.write ('<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/mac.css" />');
        }
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

        <!-- TODO: -->
        <?php if (isset($jscrypt)) : ?>
            <script src="<?php echo SRC_URL ?>/view/js/sha1.min.js"></script>
        <?php endif; ?>

        <?php if ($this->superform) : ?>
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

        <!--Para calendar -->
        <?php if (isset($jsreq_calendar)) : ?>
          <link href="<?php echo SRC_URL ?>/view/css/calendar/fullcalendar.css" rel="stylesheet" />
          <script src="<?php echo SRC_URL ?>/view/js/calendar/moment.min.js"></script>
          <script src="<?php echo SRC_URL ?>/view/js/calendar/jquery.min.js"></script>
          <script src="<?php echo SRC_URL ?>/view/js/calendar/fullcalendar.js"></script>
          <script src="<?php echo SRC_URL ?>/view/js/calendar/lang/es.js"></script>
          <script src="<?php echo SRC_URL ?>/view/js/calendar/gcal.js"></script>
          <script src="<?php echo SRC_URL ?>/view/js/calendar/custom_calendar.js"></script>

      <?php endif; ?>

      <?php if (!isset($jsreq_calendar)) : ?>
      <script src="<?php echo SRC_URL ?>/view/js/calendar/moment.min.js"></script>
      <script src="<?php echo SRC_URL ?>/view/js/calendar/lang/es.js"></script>
      <script src="<?php echo SRC_URL ?>/view/js/calendar/home_calendar.js"></script>

      <?php endif; ?>

    <?php $this->stop() ?>

    </head>

    <body<?php if ($this->bodyClass) echo ' class="' . $this->bodyClass . '"' ?>>


    <noscript><div id="noscript">Please enable JavaScript</div></noscript>

    <div id="wrapper">


    <?php $this->section('header') ?>
        <?=$this->insert("partials/header")?>
    <?php $this->stop() ?>

    <?php $this->section('sub-header') ?>
    <?php $this->stop() ?>

    <?php echo $this->supply('messages', $this->insert("partials/header/messages")) ?>

    <?php $this->section('content') ?>
    <?php $this->stop() ?>


    </div>

    <?php $this->section('footer') ?>

        <?=$this->insert("partials/footer")?>

        <?=$this->insert("partials/footer/analytics")?>

        <?=$this->insert("partials/footer/javascript")?>

    <?php $this->stop() ?>

    </body>
</html>
