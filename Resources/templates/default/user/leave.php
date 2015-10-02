<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'user-login';
// para que el prologue ponga el código js para botón facebook en el bannerside
$fbCode = $this->text_widget($this->text('social-account-facebook'), 'fb');

$error = $this->error;
$email = $this->email;
$message = $this->message;

$this->layout('layout', [
    'bodyClass' => 'user-login',
    ]);

$this->section('content');

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

                <h2><?php echo Text::get('login-leave-header'); ?></h2>

                <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
                <?php endif ?>
                <?php if (!empty($message)): ?>
                <p><?php echo $message; ?></p>
                <?php endif ?>

                <form action="<?php echo SEC_URL; ?>/user/leave" method="post">
                    <div class="email">
                        <label><?php echo Text::get('login-recover-email-field'); ?>
                        <input type="text" name="email" value="<?php echo $email?>" /></label>
                    </div>

                    <div class="message">
                        <label for="leave-message"><?php echo Text::get('login-leave-message'); ?></label>
                        <textarea id="leave-message" name="reason" cols="50" rows="5"><?php echo $reason ?></textarea>
                    </div>

                    <input type="submit" name="leaving" value="<?php echo Text::get('login-leave-button'); ?>" />

                </form>

            </div>
        </div>

    </div>


<?php $this->replace() ?>
