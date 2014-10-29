<?php 
$bodyClass = 'about';
include 'view/prologue.html.php';
include 'view/header.html.php';
?>
<?php if (\NODE_ID == \GOTEO_NODE) : ?>
    <div id="sub-header">
        <div>
            <h2><?php echo $this['description']; ?></h2>
        </div>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

    <div id="main">

        <div class="widget">
            <h3 class="title"><?php echo $this['name']; ?></h3>
            <?php echo $this['content']; ?>
        </div>

    </div>
    
<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>