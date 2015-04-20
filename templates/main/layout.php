<?php
use Goteo\Application\Lang;

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?=$title?></title>
        <link rel="icon" type="image/png" href="/myicon.png" />

        <?=$this->section('head-meta', $this->fetch("$theme::partials/header/metas", $this->engine->getData("$theme::layout")))?>


    <!-- build:css view/css/goteo.css -->
    <link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/goteo.css" />
    <!-- endbuild -->

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


    </head>

    <body<?php if (isset($bodyClass)) echo ' class="' . $this->e($bodyClass) . '"' ?>>
<?php


if (isset($fbCode)) : ?>
<div id="fb-root"></div>
<script type="text/javascript">(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/<?php echo Lang::getLocale(); ?>/all.js#xfbml=1&appId=189133314484241";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php endif; ?>

        <script type="text/javascript">
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
        </script>
        <noscript><div id="noscript">Please enable JavaScript</div></noscript>

        <div id="wrapper">


            <?php if($this->section('sub-header')): ?>
                <div id="sub-header">
                <?=$this->section('sub-header')?>
                </div>
            <?php endif ?>

            <?=$this->section('messages', $this->fetch("$theme::partials/header/messages"))?>

            <?=$this->section('content')?>


        </div>


        <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                         $("#lang").hover(function(){
                           //desplegar idiomas
                           try{clearTimeout(TID_LANG)}catch(e){};
                           var pos = $(this).offset().left;
                           $('ul.lang').css({left:pos+'px'});
                           $("ul.lang").fadeIn();
                           $("#lang").css("background","#808285 url('<?php echo SRC_URL; ?>/view/css/bolita.png') 4px 7px no-repeat");

                       },function() {
                           TID_LANG = setTimeout('$("ul.lang").hide()',100);
                        });
                        $('ul.lang').hover(function(){
                            try{clearTimeout(TID_LANG)}catch(e){};
                        },function() {
                           TID_LANG = setTimeout('$("ul.lang").hide()',100);
                           $("#lang").css("background","#59595C url('<?php echo SRC_URL; ?>/view/css/bolita.png') 4px 7px no-repeat");
                        });


                    });
                    jQuery(document).ready(function ($) {
                         $("#currency").hover(function(){
                           //desplegar idiomas
                           try{clearTimeout(TID_CURRENCY)}catch(e){};
                           var pos = $(this).offset().left;
                           $('ul.currency').css({left:pos+'px'});
                           $("ul.currency").fadeIn();
                           $("#currency").css("background","#808285");

                       },function() {
                           TID_CURRENCY = setTimeout('$("ul.currency").hide()',100);
                        });
                        $('ul.currency').hover(function(){
                            try{clearTimeout(TID_CURRENCY)}catch(e){};
                        },function() {
                           TID_CURRENCY = setTimeout('$("ul.currency").hide()',100);
                           $("#currency").css("background","#59595C");
                        });


                    });
        </script>


    <?=$this->section('analytics', $this->fetch("$theme::partials/header/analytics"))?>

    <?=$this->section('javascript', $this->fetch("$theme::partials/header/javascript"))?>

    </body>
</html>
