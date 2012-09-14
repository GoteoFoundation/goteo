<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'home';

include 'view/node/prologue.html.php';
include 'view/node/header.html.php';
?>

<?php if (isset($this['side_order']['searcher']) || isset($this['side_order']['categories'])) : ?>
<!-- funcion jquery para mostrar uno y ocultar el resto -->
<script type="text/javascript">
    $(function(){
        $(".show_cat").click(function (event) {
            event.preventDefault();

            if ($("#node-projects-"+$(this).attr('href')).is(":visible")) {
                $(".button").removeClass('current');
                $(".rewards").removeClass('current');
                $(".categories").removeClass('current');
                $(".node-projects").hide();
            } else {
                $(".button").removeClass('current');
                $(".rewards").removeClass('current');
                $(".categories").removeClass('current');
                $(".node-projects").hide();
                $(this).parents('div').addClass('current');
                $("#node-projects-"+$(this).attr('href')).show();
            }

        });
    });
</script>
<?php endif; ?>

<div id="node-main">
    <div id="side">
    <?php foreach ($this['side_order'] as $sideitem=>$sideitemName) {
        if (!empty($this[$sideitem])) echo new View("view/node/side/{$sideitem}.html.php", $this);
    } ?>
    </div>

    <div id="content">
    <?php
    // primero los ocultos, los destacados si esta el buscador lateral lo ponemos anyway
    if (isset($this['side_order']['searcher'])) echo new View("view/node/home/discover.html.php", $this);
    if (isset($this['side_order']['categories'])) echo new View("view/node/home/discat.html.php", $this);
    if (!empty($this['page']->content)) {
        if (isset($this['searcher']['promote'])) echo new View("view/node/home/promotes.html.php", $this);
        echo $this['page']->content;
    } else {
        foreach ($this['order'] as $item=>$itemName) {
            if (!empty($this[$item])) echo new View("view/node/home/{$item}.html.php", $this);
        }
    }
    ?>
    </div>
</div>
<?php include 'view/node/footer.html.php'; ?>
<?php include 'view/epilogue.html.php'; ?>