<?php
use Goteo\Library\Text;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Newsletter Goteo</title>
<style type="text/css">
<!--
body {
	margin: 0px;
	padding: 0px;
	font-family: "Helvetica Neue", Helvetica, Arial, Geneva, sans-serif;
	color:#58595B;
	padding-left: 20px;
	background-color: #f1f1f1;
}


.header-bar { 
	width: 100%;
	height: 22px;
	line-height:22px;
	font-size:10px;
	color:#cccccc;
	background-color:#58595B;
}

.header-bar a { 
	color:#ffffff;
}


.header { 
	width: 100%;
	background-color:#CDE4E5;
	padding-top:7px;
	padding-bottom:7px;
}

.header-element {
	margin-left:50px;
	}

.content {
	width:600px;
	margin-left:50px;
	}

.message {
	font-size:14px;
	padding-bottom:20px;
	padding-top:20px;
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

.disclaimer {
	font-size:11px;
	color:#20B3B2;
	padding-bottom:10px;
	padding-top:10px;
	border-bottom: 1px solid #20B3B2;
	border-top: 1px solid #20B3B2;
	}
	
.goteo-url {
	font-size:12px;
	color:#20B3B2;
	padding-top:10px;
	padding-bottom:10px;
	}
	
.goteo-url a {
	color:#20B3B2;
	text-decoration:none;
	}

.descubre {
	color:#E32526;
	font-size:14px;
	padding-top:5px;
    text-transform: uppercase;
	}
	
.descubre a {
	color:#E32526;
	text-decoration:none;
	}
	
.crea {
	color:#20B3B2;
	font-size:14px;
	padding-top:5px;
	padding-bottom:10px;
    text-transform: uppercase;
	}
	
.crea a {
	color:#20B3B2;
	text-decoration:none;
	}

.follow {
	font-size:11px;
	padding-bottom:10px;
	}

.facebook a {
	color:#233E99;
	text-decoration:none;
	}

.twitter a {
	color:#00AEEF;
	text-decoration:none;
	}
	
.rss a {
	color:#F15A29;
	text-decoration:none;
	}
	
.unsuscribe {
	font-size:11px;
	text-align:right;
	padding-bottom:10px;
	padding-top:10px;
	border-top: 1px solid #20B3B2;
	}
	
.unsuscribe a {
color:#20B3B2;
	text-decoration:none;
	}

.footer-bar { 
	width: 100%;
	height: 22px;
	line-height:22px;
	font-size:10px;
	color:#ffffff;
	background-color:#58595B;
	text-align:right
}

.footer-bar a { 
	color:#ffffff;
	text-decoration:none
}


.footer-element {
	margin-right:50px;
	}

.intro {
	width: 634px;
	background-color: #ffffff;
	font-size: 18px;
	padding: 20px 20px 5px;
}

.intro-tit {
	font-size: 21px;
	font-weight: bold;
}

.section-tit {
	width: 634px;
	font-size: 18px;
	font-weight: bold;
	margin-top: 30px;
	padding-top: 20px;
	padding-left: 20px;
}

.project {
	width: 644px;
	background-color: #ffffff;
	padding: 20px 10px 10px 20px;
	margin-top: 20px;
}

.project-header {
	color: #38b5b1;
	font-weight: bold;
}

.project-header-desc {
	font-size: 14px;
	font-weight: normal;
	font-style: normal;
}

.project-tit {
	font-size: 14px;
	font-weight: bold;
	padding-left: 5px;
}

.project-autor {
	font-size: 11px;
	color: #434343;
	vertical-align: top;
	padding-bottom: 5px;
	padding-top: 5px;
	color: #929292;
	padding: 5px;
}

.project-img {
	width: 226px;
}

.project-txt {
	font-size: 12px;
	color: #929292;
	vertical-align: top;
	border-right: 2px solid #f1f1f1;
	line-height: 15px;
	width: 190px;
	padding-right: 10px;
}

.project-categoria {
	font-size: 10px;
	color: #434343;
}

.project-min {
	font-size: 10px;
	vertical-align: top;
	color: #96238F;
}

.project-opt {
	font-size: 10px;
	vertical-align: top;
	color: #ba6fb6;
}

.project-valor2 {
	font-weight: bold;
	color: #ba6fb6;
	font-size: 21px;
}

.project-valor1 {
	font-weight: bold;
	color: #96238F;
	font-size: 21px;
}

.project-quedan {
	font-size: 11px;
	line-height: 14px;
}

.project-dias {
	font-weight: bold;
	font-size: 14px;
	line-height: 14px;
}

.line {
	width: 25px;
	height: 2px;
	border-bottom: 1px solid #38b5b1;
	margin-bottom: 15px;
}

.camp {
	width: 634px;
	background-color: #ffffff;
	padding: 10px 20px;
	margin-bottom: 20px;
	margin-top: 20px;
}

.camp-logo {
	width: 155px;
	text-align: center;
	border-right: 2px solid #f1f1f1;
}

.camp-tit {
	width: 210px;
	font-size: 16px;
	font-weight: bold;
	color: #38b5b1;
	border-right: 2px solid #f1f1f1;
	line-height: 21px;
}

.camp-sebuscan {
	font-size: 16px;
	font-weight: bold;
	color: #EF4243;
}

.camp-desc {
	color: #96238F;
	font-size: 12px;
	height: 60px;
	vertical-align: top;
}

.camp-txt {
	font-size: 10px;
	vertical-align: top;
}

.camp-valor1 {
	font-weight: bold;
	color: #ba6fb6;
	font-size: 21px;
}

.camp-valor2 {
	font-weight: bold;
	color: #96238F;
	font-size: 21px;
}

.camp-fecha1 {
	font-weight: bold;
	font-size: 21px;
}

.camp-fecha2 {
	font-weight: bold;
	font-size: 14px;
}
-->
</style>
</head>

<body>

<?php if (isset($this['sinoves'])) : ?><div class="header-bar"><span class="header-element"><?php echo Text::html('mailer-sinoves', $this['sinoves']); ?></span></div><?php endif; ?>
<div class="header"><span class="header-element"><img src="cid:logo" alt="Goteo"/></span></div>

<div class="content">

<div><!--mensaje - contenido-->
  <?php echo $this['content'] ?>
</div>  
  
<div class="disclaimer"><?php echo Text::get('mailer-disclaimer') ?></div>

<div class="goteo-url"><a href="<?php echo SITE_URL ?>">www.goteo.org</a></div>
<div class="descubre"><a href="<?php echo SITE_URL . '/discover' ?>"><?php echo Text::get('regular-discover'); ?></a></div>
<div class="crea"><a href="<?php echo SITE_URL . '/project/create' ?>"><?php echo Text::get('regular-create'); ?></a></div>

<div class="follow">S&iacute;guenos en:<br />
  <span class="facebook"><a href="<?php echo Text::get('social-account-facebook') ?>">facebook</a></span> |  <span class="twitter"><a href="<?php echo Text::get('social-account-twitter') ?>">twitter</a></span> |   <span class="rss"><a rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo SITE_URL. '/rss' ?>">RSS</a></span></div>

<div class="unsuscribe"><?php echo Text::get('newsletter-block'); ?></div>

</div>

<div class="footer-bar"><span class="footer-element"><?php echo Text::get('footer-platoniq-iniciative') ?> <strong><a href="http://platoniq.net">Platoniq</a></strong></span></div>

</body>
</html>