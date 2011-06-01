<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'dashboard';

include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

        <div id="main">

            <p><?php echo $this['message']; ?></p>

            <?php echo new View ('view/dashboard/menu.html.php', $this); ?>

        </div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';