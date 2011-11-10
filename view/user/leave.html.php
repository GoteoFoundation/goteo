<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'user-login';
include 'view/prologue.html.php';
include 'view/header.html.php';

$error = $this['error'];
$message = $this['message'];
extract($_POST);
if (!isset($_POST['email']) && isset($_GET['email'])) {
    $email = $_GET['email'];
}
?>
<div id="sub-header">
	<div class="clearfix">
		<div>
			<h2><?php echo Text::html('login-banner-header'); ?></h2>
		</div>
		<div class="mod-pojctopen"><?php echo Text::html('open-banner-header'); ?></div>
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

                <form action="/user/leave" method="post">
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

<?php include 'view/footer.html.php' ?>