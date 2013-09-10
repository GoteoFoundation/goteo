<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Library\Page;

$page = Page::get('big-error');

include 'view/prologue.html.php';
include 'view/header.html.php';
?>

    <div id="sub-header">
        <div>
            <h2><?php echo $error->getMessage() ?>!</h2>
        </div>
    </div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

    <div id="main">
        <div class="widget">
            <h3 class="title"><?php echo $page->name; ?></h3>
            <?php echo $page->content; ?>
        </div>
    </div>

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>