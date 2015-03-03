<?php

use \Goteo\Library\Text;

if (NODE_ID != GOTEO_NODE) {
    include __DIR__ . '/node/prologue.html.php';
    return;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

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

        <!-- vigilante de sesion -->
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/watchdog.js"></script>

        <?php if (!isset($_SESSION['impersonating']) && $_SESSION['user'] instanceof \Goteo\Model\User && empty($_SESSION['user']->geoloc) && !$_SESSION['user']->geologed && !$_SESSION['user']->unlocable) : ?>
        <!-- geologin -->
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/geologin.js"></script>
        <?php endif; ?>


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
          <script src="<?php echo SRC_URL ?>/view/js/calendar/gcal.js"></script>

          <script>

            $(document).ready(function() {
  
            $('#calendar').fullCalendar({

            googleCalendarApiKey: 'AIzaSyBtKe8e-5DfwDeKFUTcrRmOU7BzXMndg1Y',
    
            // Goteo calendario publico
            events: 'l44ukbe8tsjlr50djnk2kl2cik@group.calendar.google.com',
      
            eventClick: function(event) {
              // opens events in a popup window
             
              $("#read-more").css( "display", "block" );;
              $("#event-description").html(event.description);
              $("#event-title").html(event.title);
              
              var event_date=moment(new Date(event.start)).format("DD | MM | YYYY");
              var event_start=moment(new Date(event.start)).format("HH:mm");
              var event_end=moment(new Date(event.end)).format("HH:mm");
    
              $("#event-date").html(event_date);
              $("#event-location").html(event.location);
              $("#event-hour").html(event_start+" - "+event_end+ " | ");

              

              //$("#event-date").html(d1);
              //alert(getDay(event.start));
              /*window.open(event.url, 'gcalevent', 'width=350,height=400');*/
              return false;
            },
      
            loading: function(bool) {
              $('#loading').toggle(bool);
            }
      
            });
    
            });

          </script>

          <style>

          #loading {
            display: none;
            position: absolute;
            top: 10px;
            right: 10px;
          }

          #calendar {
            max-width: 800px;
            margin: 0 auto;
          }

          </style>

      <?php endif; ?>

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

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17744816-4']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<script>
var _prum = [['id', '5434f3beabe53dcd6ff6f0cf'],
             ['mark', 'firstbyte', (new Date()).getTime()]];
(function() {
    var s = document.getElementsByTagName('script')[0]
      , p = document.createElement('script');
    p.async = 'async';
    p.src = '//rum-static.pingdom.net/prum.min.js';
    s.parentNode.insertBefore(p, s);
})();
</script>

    </head>

    <body<?php if (isset($bodyClass)) echo ' class="' . htmlspecialchars($bodyClass) . '"' ?>>
<?php if (isset($fbCode)) : ?>
<div id="fb-root"></div>
<script type="text/javascript">(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/<?php echo \Goteo\Library\Lang::locale(); ?>/all.js#xfbml=1&appId=189133314484241";
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
