<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'user-login';
// para que el prologue ponga el código js para botón facebook en el bannerside
$fbCode = Text::widget(Text::get('social-account-facebook'), 'fb');
include 'view/prologue.html.php';
include 'view/header.html.php';

$error = $this['error'];
$message = $this['message'];
extract($_POST);
?>
<div id="sub-header">
	<div class="clearfix">
		<div class="subhead-banner">
			<h2 class="message"><?php echo Text::html('login-banner-header'); ?></h2>
		</div>
		<div class="mod-pojctopen"><?php echo Text::html('open-banner-header', $fbCode); ?></div>
	</div>
</div>
    <div id="main">

        <div class="login">

            <div>

                <h2><?php echo Text::get('login-recover-header'); ?></h2>

                <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
                <?php endif ?>
                <?php if (!empty($message)): ?>
                <p><?php echo $message; ?></p>
                <?php endif ?>

                <form action="/user/recover" method="post">
                    <div class="username">
                        <label><?php echo Text::get('login-recover-username-field'); ?>
                        <input type="text" name="username" value="<?php echo $username?>" /></label>
                    </div>

                    <div class="email">
                        <label><?php echo Text::get('login-recover-email-field'); ?>
                        <input type="text" name="email" value="<?php echo $email?>" /></label>
                    </div>

                    <input type="submit" name="recover" value="<?php echo Text::get('login-recover-button'); ?>" />

                </form>

            </div>
        </div>

    </div>

<?php include 'view/footer.html.php' ?>