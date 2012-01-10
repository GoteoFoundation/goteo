<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo GOTEO_META_TITLE ?></title>
        <link rel="icon" type="image/png" href="/myicon.png" />
        <meta name="description" content="<?php echo GOTEO_META_DESCRIPTION ?>" />
        <meta name="keywords" content="<?php echo GOTEO_META_KEYWORDS ?>" />
        <meta name="author" content="<?php echo GOTEO_META_AUTHOR ?>" />
        <meta name="copyright" content="<?php echo GOTEO_META_COPYRIGHT ?>" />
        <meta name="robots" content="all" />
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />	

		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/tipsy/tipsy.css" />		
		<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/call/splash.css" />

      <!--[if IE]>
      <link href="<?php echo SRC_URL ?>/view/css/ie.css" media="screen" rel="stylesheet" type="text/css" />
      <![endif]-->

        <script type="text/javascript">
        if(navigator.userAgent.indexOf('Mac') != -1)
		{
			document.write ('<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/mac.css" />');
		}
	    </script>
        <?php if (!isset($useJQuery) || !empty($useJQuery)): ?>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.tipsy.min.js"></script>
        <?php endif ?>
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
				
				(function(){var a=document.body;var b=document.getElementById("bgimage").getElementsByTagName("img")[0];var c={};var d=b.src;setInterval(function(){window.scrollTo(0,0);if(b.complete){if(a.clientWidth!=c.w||a.clientHeight!=c.h||b.src!=d){d=b.src;c.w=a.clientWidth;c.h=a.clientHeight;var e=Math.round(c.h*(b.offsetWidth/b.offsetHeight));b.style.width=(c.w>e?c.w:e)+"px"}}},300)})()				
				
            });
        </script>


        <noscript><!-- Please enable JavaScript --></noscript>

		<div id="bgimage">
			<div><img alt="<?php echo $call->name ?>" src="<?php echo $call->image->getLink(2000, 2000) ?>" /></div>
		</div>

        <div id="wrapper">
			
