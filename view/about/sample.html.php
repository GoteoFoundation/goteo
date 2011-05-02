<?php 
$bodyClass = 'about';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

    <div id="main">

        <h2><?php echo $this['name']; ?></h2>
        <p><?php echo $this['title']; ?></p>

        <div id="content"><?php echo $this['content']; ?></div>

    </div>
    
    <?php include 'view/footer.html.php' ?>