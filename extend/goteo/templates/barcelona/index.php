<?php

use Goteo\Core\View;

$this->layout("layout", [
    'bodyClass' => 'home'
    ]);

?>

<?php /* MAIN PAGE CONTENT */ ?>

<?php $this->section('content') ?>

<div id="node-main">
    <div id="side">
    <?php foreach ($this->side_order as $sideitem => $sideitemName) {
        if ($sideitem) echo View::get("node/side/{$sideitem}.html.php", $this->vars);
    } ?>
    </div>

    <div id="content">
    <?php
    // primero los ocultos, los destacados si esta el buscador lateral lo ponemos anyway
    if ($this->side_order['searcher']) echo View::get('node/home/discover.html.php', $this->vars);
    if ($this->side_order['categories']) echo View::get('node/home/discat.html.php', $this->vars);
    if ($this->page->content) {
        if ($this->searcher['promote']) echo View::get('node/home/promotes.html.php', $this->vars);
        echo '<div id="node-about-content" class="widget">' . $this->page->content . '</div>';
    } else {
        foreach ($this->order as $item => $itemName) {
            if ($this->$item) echo View::get("node/home/{$item}.html.php", $this->vars);
        }
    }
    ?>
    </div>
</div>
<?php $this->stop() ?>

