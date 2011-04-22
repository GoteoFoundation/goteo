<?php 
$bodyClass = 'invest';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>
        
        <div id="main">

            <p><?php echo $this['message']; ?></p>

            <h2><?php echo $this['project']->name; ?></h2>

            <p>Widghet / Difundir en mi red</p>

        </div>
    
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';