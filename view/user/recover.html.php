<?php

use Goteo\Core\View;

$bodyClass = 'user-login';
include 'view/prologue.html.php';
include 'view/header.html.php';

$error = $this['error'];
$message = $this['message'];
extract($_POST);
?>
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