<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'community about';

include 'view/prologue.html.php';
include 'view/header.html.php';
?>

    <div id="sub-header">
        <div>
            <h2 style="margin-bottom:5px"><?php echo $this['description']; ?></h2>
        </div>
    </div>

    <div id="main">

    <?php if ($this['show'] == 'activity') : /* ahora el feed*/ ?>
        <?php echo new View('view/community/feed.html.php', $this) ?>
    <?php /* Hasta aqui el feed*/ else : /*a ahora sharemates global*/ ?>
        <div class="center">
            <?php echo new View('view/community/sharemates.html.php', $this) ?>
        </div>
        <div class="side">
            <?php echo new View('view/community/investors.html.php', $this) ?>
        </div>
    <?php /* Hasta qui sharemates global */ endif; ?>

    </div>

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>