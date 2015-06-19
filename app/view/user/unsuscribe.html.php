<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'user-login';
include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

$error = $vars['error'];
$message = $vars['message'];
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

<?php include __DIR__ . '/../footer.html.php' ?>
