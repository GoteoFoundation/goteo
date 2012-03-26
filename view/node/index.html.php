<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'home';

include 'view/node/prologue.html.php';
include 'view/node/header.html.php';
?>
<div id="main">

    <?php
    foreach ($this['order'] as $item=>$itemData) {
        if (!empty($this[$item])) echo new View("view/home/{$item}.html.php", $this);
    } ?>

</div>
<?php include 'view/node/footer.html.php'; ?>
<?php include 'view/epilogue.html.php'; ?>