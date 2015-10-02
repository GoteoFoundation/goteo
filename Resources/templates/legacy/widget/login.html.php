<?php

use Goteo\Library\Text,
	Goteo\Library\Currency;

$select_currency=Currency::$currencies[$_SESSION['currency']]['html'];
$amount=0;

?>
<h3 class="beak"><?php echo Text::get('user-login-required-to_invest') ?></h3>

<h5 class="login-widget-title"></h5>
<div class="login-widget-form">

<h5 class="login-widget-title">Accede con tu usuario de Goteo: </h5>
<input class="login-widget-input" type="text" id="username" name="username" placeholder="Email o usuario" value="" />
<input class="login-widget-input" type="password" id="password" name="password" placeholder="Password" value="" />

<label class="login-widget-label"><input type="checkbox" name="keep-session" value="1" /><span class="chkbox"></span>Mantenerme conectado</label>
<div class="buttons">
    <button type="submit" class="button green" name="go-login" value="">INICIAR SESION</button>
</div>
<div class="login-widget-remember">
<a href="">¿Has olvidado tu contraseña?</a> | <a href="">¿Eres nuevo en Goteo? Registrate</a>
</div>



</div>

<div class="login-widget-social">
	<h5 class="login-widget-title">Conecta via: </h5>
	<ul>
		<li class="icon" ><a href="/user/oauth?provider=facebook"><img src="/view/css/facebook.png" /></a></li>
		<li class="icon margin" ><a href="/user/oauth?provider=twitter"><img src="/view/css/twitter.png" /></a></li>
		<li class="icon margin" ><a href="/user/oauth?provider=google"><img src="/view/css/openid-google.png" /></a></li>
		<li class="icon margin" ><a href="/user/oauth?provider=Yahoo"><img src="/view/css/openid-yahoo.png" /></a></li>
		<li class="icon margin" ><a href="/user/oauth?provider=Ubuntu"><img src="/view/css/openid-ubuntu.png" /></a></li>
		<li class="icon" ><a href="/user/oauth?provider=linkedin"><img src="/view/css/linkedin.png" /></a></li>
		<li class="icon margin"><img src="/view/css/openid-openid.png" /></li>
	</ul>
</div>

<div class="reminder reminder-signed"><?php echo Text::get('invest-alert-investing') ?> <span class="amount-reminder"><?php echo $select_currency; ?></span><span id="amount-reminder"><?php echo $amount ?></span>
<div id="reward-reminder"></div>
<?php
    if ($_SESSION['currency'] != Currency::DEFAULT_CURRENCY ) :
        echo '<div>'.Text::html('currency-alert', \amount_format($amount, 3, true, true) ).'</div>';
    endif;
?>
</div>

<div class="reminder"><?php echo Text::html('faq-payment-method'); ?></div>

<?php if ($_SESSION['currency'] != Currency::DEFAULT_CURRENCY ) : ?>
    <div class="reminder"><?php echo Text::html('currency-alert', \amount_format($amount, 0, true, true) ); ?></div>
<?php endif; ?>
