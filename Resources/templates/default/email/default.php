<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?= $this->subject ? $this->subject : 'Goteo Mailer'  ?></title>
<style type="text/css">
<!--
body {
	margin: 0px;
	padding: 0px;
	font-family: Arial, Helvetica, sans-serif;
	color:#58595B;
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

-->
</style>
</head>

<body>

<?php if ($this->alternate) : ?><div class="header-bar"><span class="header-element"><?= $this->text('mailer-sinoves', $this->raw('alternate')) ?></span></div><?php endif ?>
<div class="header"><span class="header-element"><img src="<?= $this->get_url() ?>/goteo_logo.png" alt="Logo" /></span></div>

<div class="content">

<div class="message">
  <?= $this->raw('content') ?>
</div>

<div class="disclaimer"><?= $this->text('mailer-disclaimer') ?></div>

<div class="goteo-url"><a href="<?= $this->get_url() ?>"><?= $this->get_config('url.main') ?></a></div>
<div class="descubre"><a href="<?= $this->get_url() . '/discover' ?>"><?= $this->text('regular-discover') ?></a></div>
<div class="crea"><a href="<?= $this->get_url() . '/project/create' ?>"><?= $this->text('regular-create') ?></a></div>

<div class="follow">Síguenos en:<br />
  <span class="facebook"><a href="<?= $this->text('social-account-facebook') ?>">facebook</a></span> |  <span class="twitter"><a href="<?= $this->text('social-account-twitter') ?>">twitter</a></span> |   <span class="rss"><a rel="alternate" type="application/rss+xml" title="RSS" href="<?= $this->get_url(). '/rss' ?>">RSS</a></span></div>

<div class="unsuscribe"><?= $this->text('mailer-baja', $this->unsubscribe) ?></div>

</div>

<div class="footer-bar"><span class="footer-element"><?= $this->text('footer-platoniq-iniciative') ?> <strong><a href="http://platoniq.net">Platoniq</a></strong></span></div>

<?php if ($this->tracker) : ?><img src="<?= $this->tracker ?>" alt="" /><?php endif ?>
</body>
</html>
