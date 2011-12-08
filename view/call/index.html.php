<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'about';

include 'view/prologue.html.php';
include 'view/header.html.php';
?>

    <div id="sub-header">
        <div>
            <h2 style="margin-bottom:5px"><?php echo $this['call']->name; ?></h2>
        </div>
    </div>

    <div id="main">

        <div class="">

            <?php echo '<pre>'.print_r($this['call'], 1).'</pre>'; ?>

        </div>

    </div>

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>