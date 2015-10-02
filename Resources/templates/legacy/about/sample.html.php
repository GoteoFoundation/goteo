<?php
$bodyClass = 'about';
include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';
?>

    <div id="sub-header">
        <div>
            <h2><?php echo $vars['description']; ?></h2>
        </div>
    </div>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

    <div id="main">

        <div class="widget">
            <h3 class="title"><?php echo $vars['name']; ?></h3>
            <?php echo $vars['content']; ?>
        </div>

    </div>

<?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
