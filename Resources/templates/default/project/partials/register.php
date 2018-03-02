<?php

use Goteo\Library\Text,
	Goteo\Application\Currency;

$select_currency=Currency::$currencies[$_SESSION['currency']]['html'];
$amount=0;

?>
<h3 class="beak"><?php echo Text::get('user-login-required-to_invest') ?></h3>
<h5 class="login-widget-title"></h5>

<div class="login-widget-form register">

	<h5 class="login-widget-title">Crea un nuevo usuario en Goteo: </h5>
		<input class="login-widget-input" type="text" id="username" name="username" placeholder="Nombre completo" value="" />
		<input class="login-widget-input" type="password" id="password" name="password" placeholder="Email" value="" />

	<div class="input-row">
		<input class="login-widget-input" type="text" id="username" name="username" placeholder="Contraseña" value="" />
		<input class="login-widget-input" type="password" id="password" name="password" placeholder="Nombre de usuario" value="" />
	</div>

	<div class="buttons">
		<button type="submit" class="button green" name="go-login" value="">REGISTRARME</button>
	</div>

</div>

<div class="reminder reminder-signed"><?php echo Text::get('invest-alert-investing') ?> <span class="amount-reminder"><?php echo $select_currency; ?></span><span id="amount-reminder"><?php echo $amount ?></span>
<div id="reward-reminder"></div>
<?php
    if ($_SESSION['currency'] != Currency::getDefault('id') ) :
        echo '<div>'.Text::html('currency-alert', \amount_format($amount, 3, true, true) ).'</div>';
    endif;
?>
</div>

<div class="reminder"><?php echo Text::html('faq-payment-method'); ?></div>

<?php if ($_SESSION['currency'] != Currency::getDefault('id') ) : ?>
    <div class="reminder"><?php echo Text::html('currency-alert', \amount_format($amount, 0, true, true) ); ?></div>
<?php endif; ?>
