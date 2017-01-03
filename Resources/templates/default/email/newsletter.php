<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Goteo Newsletter<?= $this->subject ? ' :: ' . $this->subject : ''  ?></title>
<style type="text/css">
/* Defaults */
h1 {
    font-size:24px;
}
h2 {
    font-size:22px;
}
h3 {
    font-size:20px;
}
h4 {
    font-size:18px;
}
h5 {
    font-size:16px;
}
h6 {
    font-size:14px;
}
a {
    color: #3AB9C2;
}
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

.btn-support {
  background: #16A5A3;
  background-image: -webkit-linear-gradient(top, #16A5A3, #24837F);
  background-image: -moz-linear-gradient(top, #16A5A3, #24837F);
  background-image: -ms-linear-gradient(top, #16A5A3, #24837F);
  background-image: -o-linear-gradient(top, #16A5A3, #24837F);
  background-image: linear-gradient(to bottom, #16A5A3, #24837F);
  -webkit-border-radius: 11;
  -moz-border-radius: 11;
  border-radius: 6px;
  font-family: Arial;
  color: #ffffff;
  font-size: 15px;
  padding: 10px 20px 10px 20px;
  text-decoration: none;
  float:right;
  margin-top: 48px;
}

.btn-support:hover {
  opacity: 0.8;
  text-decoration: none;
}

</style>
</head>

<body style="margin: 0px; padding: 0px; font-family: Helvetica, Arial, Geneva, sans-serif; color:#58595B; padding-left: 20px; background-color: #f1f1f1;">

<?php if ($this->alternate) : ?>
    <div style="width: 100%; height: 22px; line-height:22px; font-size:10px; color:#cccccc; background-color:#58595B;"><span style="margin-left:50px;"><?= $this->text('mailer-sinoves', $this->raw('alternate') . '" style="color:white;') ?></span></div>
<?php endif ?>
<div style="width: 100%; background-color:#CDE4E5; padding-top:7px; padding-bottom:7px;"><span style="margin-left:50px;"><img src="<?= $this->get_url() ?>/goteo_logo.png" alt="Logo" /></span></div>

<div style="width:630px; margin-left:50px; margin-top:20px;">

<div><!--mensaje - contenido-->
  <?= $this->raw('content') ?>
</div>

<div style="font-size:11px; color:#20B3B2; padding-bottom:10px; padding-top:10px; border-bottom: 1px solid #20B3B2; border-top: 1px solid #20B3B2;"><?= $this->text('mailer-disclaimer') ?></div>

<div style="font-size:12px; color:#20B3B2; padding-top:10px; padding-bottom:10px;"><a href="<?= $this->get_config('url.main') ?>" style="color:#20B3B2; text-decoration:none;">www.goteo.org</a></div>

<a class="btn-support" style="background: #16A5A3; background-image: -webkit-linear-gradient(top, #16A5A3, #24837F); background-image: -moz-linear-gradient(top, #16A5A3, #24837F); background-image: -ms-linear-gradient(top, #16A5A3, #24837F); background-image: -o-linear-gradient(top, #16A5A3, #24837F); background-image: linear-gradient(to bottom, #16A5A3, #24837F); -webkit-border-radius: 11; -moz-border-radius: 11; border-radius: 6px; font-family: Arial; color: #ffffff; font-size: 15px; padding: 10px 20px 10px 20px; text-decoration: none; float:right; margin-top: 48px;" href="https://fundacion.goteo.org/donaciones/">
  <?= $this->text('support-our-mission') ?>
</a>
<div style="color:#E32526; font-size:14px; padding-top:5px; text-transform: uppercase;"><a href="<?= $this->get_config('url.main') . '/discover' ?>" style="color:#E32526; text-decoration:none;"><?= $this->text('regular-discover') ?></a></div>
<div style="color:#20B3B2; font-size:14px; padding-top:5px; padding-bottom:10px; text-transform: uppercase;"><a href="<?= $this->get_config('url.main') . '/project/create' ?>" style="color:#20B3B2; text-decoration:none;"><?= $this->text('regular-create') ?></a></div>

<div style="font-size:11px; padding-bottom:10px;"><?= $this->text('footer-header-social') ?><br />
  <span><a href="<?= $this->text('social-account-facebook') ?>" style="color:#233E99; text-decoration:none;">facebook</a></span> | <span><a href="<?= $this->text('social-account-twitter') ?>" style="color:#00AEEF; text-decoration:none;">twitter</a></span> |  <span><a href="<?= $this->text('social-account-identica') ?>" style="color:#C42F31; text-decoration:none;">identica</a></span> | <span><a rel="alternate" type="application/rss+xml" title="RSS" href="<?= $this->get_config('url.main'). '/rss' ?>" style="color:#F15A29; text-decoration:none;">RSS</a></span></div>

<div style="width:630px;font-size:11px; text-align:right; padding-bottom:10px; padding-top:10px; border-top: 1px solid #20B3B2;"><?= $this->text('newsletter-block', $this->raw('unsubscribe')) ?></div> <!-- enlace color:#20B3B2; text-decoration:none; -->

</div>

<div style="width: 100%; height: 22px; line-height:22px; font-size:10px; color:#ffffff; background-color:#58595B; text-align:right;"><span style="margin-right:50px;"><?= $this->text('footer-platoniq-iniciative') ?> <strong><a href="http://platoniq.net" style="color:#ffffff; text-decoration:none;" >Platoniq</a></strong></span></div>
<?php if ($this->tracker) : ?><img src="<?= $this->tracker ?>" alt="" /><?php endif ?>
</body>
</html>
