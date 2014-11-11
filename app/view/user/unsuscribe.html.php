<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'user-login';
include 'view/prologue.html.php';
include 'view/header.html.php';

$error = $this['error'];
$message = $this['message'];
?>
    <div id="main">

        <div class="login">

            <div>

                <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
                <?php endif ?>
                <?php if (!empty($message)): ?>
                <p><?php echo $message; ?></p>
                <?php endif ?>

            </div>
        </div>

    </div>

<?php include 'view/footer.html.php' ?>