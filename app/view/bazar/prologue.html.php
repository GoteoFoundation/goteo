<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $this['metas_seo']['title']; ?></title>
        <link rel="icon" type="image/png" href="/myicon.png" />
        <meta name="description" content="<?php echo $this['metas_seo']['description']; ?>">
        <meta name="viewport" content="width=device-width">

        <meta property="og:title" content="<?php echo $this['ogmeta']['title'] ?>" />
        <meta property="og:type" content="activity" />
        <meta property="og:site_name" content="Goteo.org" />
        <meta property="og:description" content="<?php echo $this['ogmeta']['description'] ?>" />
        <?php foreach($this['ogmeta']['image'] as $ogimg) : ?>
        <meta property="og:image" content="<?php echo $ogimg; ?>" />
        <?php endforeach; ?>
        <meta property="og:url" content="<?php echo $this['ogmeta']['url'] ?>" />


        <link rel="stylesheet" href="<?php echo SRC_URL; ?>/view/bazar/css/normalize.css" />
        <link rel="stylesheet" href="<?php echo SRC_URL; ?>/view/bazar/css/common.css" />
        <link rel="stylesheet" href="<?php echo SRC_URL; ?>/view/bazar/css/minimobile.css" media="only screen and (max-width:340px)" />
        <link rel="stylesheet" href="<?php echo SRC_URL; ?>/view/bazar/css/mobile.css" media="only screen and (min-width:340px) and (max-width:750px)" />
        <link rel="stylesheet" href="<?php echo SRC_URL; ?>/view/bazar/css/tablet.css" media="only screen and (min-width:750px) and (max-width:1023px)" />
        <link rel="stylesheet" href="<?php echo SRC_URL; ?>/view/bazar/css/pc.css" media="only screen and (min-width:1024px) and (max-width:1400px)" />
        <link rel="stylesheet" href="<?php echo SRC_URL; ?>/view/bazar/css/bigpc.css" media="only screen and (min-width:1400px)" />

        <script type="text/javascript" src="<?php echo SRC_URL; ?>/view/bazar/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <script type="text/javascript" src="<?php echo SRC_URL; ?>/view/bazar/js/vendor/jquery-1.10.1.min.js"></script>
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

    <body>
        <script type="text/javascript">
            $(function () {
                $('.activable').hover(
                    function () { $(this).addClass('active') },
                    function () { $(this).removeClass('active') }
                );
            });
        </script>
        <noscript><div id="noscript">Please enable JavaScript</div></noscript>
