<?php
use Goteo\Library\Text;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Goteo Newsletter</title>
<style type="text/css">
.message {
	font-size:14px; padding-bottom:20px; padding-top:20px;
	}

.message-highlight-red {
	color:#E32526;
	}

.message-highlight-blue {
	color:#20B3B2;
	}

.message-highlight-blue a {
	color:#20B3B2;
	text-decoration:none;
	}

img a {
	border:none;
	border-style:none
	}
</style>
</head>

<body style="margin: 0px; padding: 0px; font-family: Helvetica, Arial, Geneva, sans-serif; color:#58595B; padding-left: 20px; background-color: #f1f1f1;">

<?php if (isset($vars['sinoves'])) : ?><div style="width: 100%; height: 22px; line-height:22px; font-size:10px; color:#cccccc; background-color:#58595B;"><span style="margin-left:50px;"><?php echo Text::html('mailer-sinoves', $vars['sinoves'].'" style="color:white;'); ?></span></div><?php endif; ?>
<div style="width: 100%; background-color:#CDE4E5; padding-top:7px; padding-bottom:7px;"><span style="margin-left:50px;"><img src="cid:logo" alt="Goteo"/></span></div>

<div style="width:630px; margin-left:50px; margin-top:20px;">

<div><!--mensaje - contenido-->
  <?php echo $vars['content'] ?>
</div>

<div style="font-size:11px; color:#20B3B2; padding-bottom:10px; padding-top:10px; border-bottom: 1px solid #20B3B2; border-top: 1px solid #20B3B2;"><?php echo Text::get('mailer-disclaimer') ?></div>

<div style="font-size:12px; color:#20B3B2; padding-top:10px; padding-bottom:10px;"><a href="<?php echo SITE_URL ?>" style="color:#20B3B2; text-decoration:none;">www.goteo.org</a></div>
<div style="color:#E32526; font-size:14px; padding-top:5px; text-transform: uppercase;"><a href="<?php echo SITE_URL . '/discover' ?>" style="color:#E32526; text-decoration:none;"><?php echo Text::get('regular-discover'); ?></a></div>
<div style="color:#20B3B2; font-size:14px; padding-top:5px; padding-bottom:10px; text-transform: uppercase;"><a href="<?php echo SITE_URL . '/project/create' ?>" style="color:#20B3B2; text-decoration:none;"><?php echo Text::get('regular-create'); ?></a></div>

<div style="font-size:11px; padding-bottom:10px;"><?php echo Text::get('footer-header-social') ?><br />
  <span><a href="<?php echo Text::get('social-account-facebook') ?>" style="color:#233E99; text-decoration:none;">facebook</a></span> | <span><a href="<?php echo Text::get('social-account-twitter') ?>" style="color:#00AEEF; text-decoration:none;">twitter</a></span> |  <span><a href="<?php echo Text::get('social-account-identica') ?>" style="color:#C42F31; text-decoration:none;">identica</a></span> | <span><a rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo SITE_URL. '/rss' ?>" style="color:#F15A29; text-decoration:none;">RSS</a></span></div>

<div style="width:630px;font-size:11px; text-align:right; padding-bottom:10px; padding-top:10px; border-top: 1px solid #20B3B2;"><?php echo Text::html('newsletter-block', $vars['baja']); ?></div> <!-- enlace color:#20B3B2; text-decoration:none; -->

</div>

<div style="width: 100%; height: 22px; line-height:22px; font-size:10px; color:#ffffff; background-color:#58595B; text-align:right;"><span style="margin-right:50px;"><?php echo Text::get('footer-platoniq-iniciative') ?> <strong><a href="http://platoniq.net" style="color:#ffffff; text-decoration:none;" >Platoniq</a></strong></span></div>

</body>
</html>
