<?php 
$bodyClass = 'about';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

    <div id="sub-header">
        <div>
            <h2><?php echo $this['name']; ?></h2>
        </div>
    </div>

    <div id="main">

        <div class="widget">
            <?php echo $this['content']; ?>
        </div>

    </div>
    
<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>