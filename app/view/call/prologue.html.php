<?php

if ($call->image instanceof Goteo\Model\Image) {
    $img_minimobile = $call->image->getLink(340, 340);
    $img_mobile = $call->image->getLink(750, 750);
    $img_tablet = $call->image->getLink(1023, 1023);
    $img_pc = $call->image->getLink(1400, 1400);
    $imghuge = $call->image->getLink(5000, 5000);
}


?>
<!DOCTYPE HTML>
<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo GOTEO_META_TITLE ?></title>
        <link rel="icon" type="image/png" href="/myicon.png" />
        <meta name="description" content="<?php echo GOTEO_META_DESCRIPTION ?>" />
        <meta name="keywords" content="<?php echo GOTEO_META_KEYWORDS ?>" />
        <meta name="author" content="<?php echo GOTEO_META_AUTHOR ?>" />
        <meta name="copyright" content="<?php echo GOTEO_META_COPYRIGHT ?>" />
        <meta name="viewport" content="width=device-width">
        <meta name="robots" content="all" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <meta property="og:title" content="<?php echo $call->name; ?>" />
        <meta property="og:description" content="<?php echo $call->subtitle; ?>" />
        <meta property="og:image" content="<?php echo SITE_URL ?>/image/<?php echo $call->logo->id; ?>" />
        <meta property="og:url" content="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>" />

		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/tipsy/tipsy.css" />
        <?php if ($bodyClass == 'splash') : ?>
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/splash.css" />
        <?php else : ?>
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/twitter.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/view.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/projects.css" />
        <?php if ($bodyClass == 'info') : ?>
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/stats.css" />
		<?php endif; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/banners.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/supporters.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/sponsors.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/extra.css" />
    <style type="text/css">
          body {
                 background: url('<?php echo $imghuge; ?>');
               }
    </style>
    <style type="text/css" media="only screen and (max-width:340px)">
          body { background: url(<?php echo $img_minimobile; ?>);

               }

    </style>
    <style type="text/css" media="only screen and (min-width:340px) and (max-width:750px)">
          body { background: url(<?php echo $img_mobile; ?>);

          }




    </style>
    <style type="text/css" media="only screen and (min-width:750px) and (max-width:1023px)">
          body { background: url(<?php echo $img_tablet; ?>);

               }
    </style>
    <style type="text/css" media="only screen and (min-width:1024px) and (max-width:1400px)">
          body { background: url(<?php echo $img_pc; ?>);

               }
    </style>
    <link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/responsive.css" />
    <link rel="stylesheet" href="<?php echo SRC_URL ?>/view/css/call/mobile.css" media="only screen and (max-width:400px)" />
    <link rel="stylesheet" href="<?php echo SRC_URL ?>/view/css/call/tablet.css" media="only screen and (min-width:400px) and (max-width:770px)" />


    <?php endif; ?>

      <!--[if IE]>
      <link href="<?php echo SRC_URL ?>/view/css/ie.css" media="screen" rel="stylesheet" type="text/css" />
      <![endif]-->

        <!--<script type="text/javascript">
        if(navigator.userAgent.indexOf('Mac') != -1)
		{
			document.write ('<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/mac.css" />');
		}-->
	    </script>
        <?php if (!isset($useJQuery) || !empty($useJQuery)): ?>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.tipsy.min.js"></script>
        <?php endif ?>
		<script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.slides.min.js"></script>
		<script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/carousel.js"></script>
        <!-- Google analytics -->
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

