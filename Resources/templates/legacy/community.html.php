<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'community about';

include __DIR__ . '/prologue.html.php';
include __DIR__ . '/header.html.php';
?>

    <div id="sub-header">
        <div>
            <h2 style="margin-bottom:5px"><?php echo $vars['description']; ?></h2>
        </div>
    </div>

    <div id="main">

    <?php if ($vars['show'] == 'activity') : /* ahora el feed*/ ?>
        <?php echo View::get('community/feed.html.php', $vars) ?>
    <?php /* Hasta aqui el feed*/ else : /*a ahora sharemates global*/ ?>
        <div class="center">
            <?php echo View::get('community/sharemates.html.php', $vars) ?>
        </div>
        <div class="side">
            <?php echo View::get('community/investors.html.php', $vars) ?>
        </div>
    <?php /* Hasta qui sharemates global */ endif; ?>

    </div>

<?php include __DIR__ . '/footer.html.php' ?>
<?php include __DIR__ . '/epilogue.html.php' ?>
