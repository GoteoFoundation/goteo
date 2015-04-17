<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'home';

include __DIR__ . '/../node/prologue.html.php';
include __DIR__ . '/../node/header.html.php';
?>

<?php if (isset($vars['side_order']['searcher']) || isset($vars['side_order']['categories'])) : ?>
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

<?php if(isset($_SESSION['messages'])) { include __DIR__ . '/../header/message.html.php'; } ?>

<div id="node-main">
    <div id="side">
    <?php foreach ($vars['side_order'] as $sideitem=>$sideitemName) {
        if (!empty($vars[$sideitem])) echo View::get("node/side/{$sideitem}.html.php", $vars);
    } ?>
    </div>

    <div id="content">
    <?php
    // primero los ocultos, los destacados si esta el buscador lateral lo ponemos anyway
    if (isset($vars['side_order']['searcher'])) echo View::get('node/home/discover.html.php', $vars);
    if (isset($vars['side_order']['categories'])) echo View::get('node/home/discat.html.php', $vars);
    if (!empty($vars['page']->content)) {
        if (isset($vars['searcher']['promote'])) echo View::get('node/home/promotes.html.php', $vars);
        echo '<div id="node-about-content" class="widget">' . $vars['page']->content . '</div>';
    } else {
        foreach ($vars['order'] as $item=>$itemName) {
            if (!empty($vars[$item])) echo View::get("node/home/{$item}.html.php", $vars);
        }
    }
    ?>
    </div>
</div>
<?php include __DIR__ . '/../node/footer.html.php'; ?>
<?php include __DIR__ . '/../epilogue.html.php'; ?>
