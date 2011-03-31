<?php 
$bodyClass = 'about';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

    <div id="main">

        <?php if (isset($title)): ?>
        <h2><?php echo htmlspecialchars($title) ?></h2>
        <?php endif ?>

    </div>
    
    <?php include 'view/footer.html.php' ?>