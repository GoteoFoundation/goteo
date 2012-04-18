<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'home';

include 'view/node/prologue.html.php';
include 'view/node/header.html.php';
?>

<div id="node-main">
    <div id="side">
    <?php foreach ($this['side_order'] as $sideitem=>$sideitemName) {
        if (!empty($this[$sideitem])) echo new View("view/node/side/{$sideitem}.html.php", $this);
    } ?>
    </div>

    <div id="content">
    <?php foreach ($this['order'] as $item=>$itemName) {
        if (!empty($this[$item])) echo new View("view/node/home/{$item}.html.php", $this);
    } ?>
    </div>
</div>
<?php include 'view/node/footer.html.php'; ?>
<?php include 'view/epilogue.html.php'; ?>