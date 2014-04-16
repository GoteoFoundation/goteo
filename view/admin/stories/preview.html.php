<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\ACL,
    Goteo\Library\Feed,
    Goteo\Model\Node,
    Goteo\Controller\Admin;

if (!isset($_SESSION['admin_menu'])) {
    $_SESSION['admin_menu'] = Admin::menu();
}

// piñones usuarios
$allowed = Admin::$supervisors[$_SESSION['user']->id];

if (isset($allowed) && !empty($this['folder']) && !in_array($this['folder'], $allowed)) {
    header('Location: /admin/');
}

$bodyClass = 'admin';

// funcionalidades con autocomplete
$jsreq_autocomplete = $this['autocomplete'];


include 'view/prologue.html.php';
include 'view/header.html.php'; 

$story = $this['story'];
?>

<div id="sub-header">
    <div class="breadcrumbs"><?php echo ADMIN_BCPATH; ?></div>
</div>

<div id="main">

    <div class="admin-center">

         <div class="widget stories-home" style="padding:0;">
            
            <div class="stories-banners-container rounded-corners-bottom" style="position:relative;">

                <?php echo new View('view/stories/story.html.php', array('story'=>$story)); ?>

           </div>

        </div>

    </div> <!-- fin center -->

</div> <!-- fin main -->

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
