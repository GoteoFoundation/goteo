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

if (isset($allowed) && !empty($vars['folder']) && !in_array($vars['folder'], $allowed)) {
    header('Location: /admin/');
}

$bodyClass = 'admin';

// funcionalidades con autocomplete
$jsreq_autocomplete = $vars['autocomplete'];


include __DIR__ . '/../../prologue.html.php';
include __DIR__ . '/../../header.html.php';

$story = $vars['story'];
?>

<div id="sub-header">
    <div class="breadcrumbs"><?php echo ADMIN_BCPATH; ?></div>
</div>

<div id="main">

    <div class="admin-center">

         <div class="widget stories-home" style="padding:0;">

            <div class="stories-banners-container rounded-corners-bottom" style="position:relative;">

                <?php echo View::get('stories/story.html.php', array('story'=>$story)); ?>

           </div>

        </div>

    </div> <!-- fin center -->

</div> <!-- fin main -->

<?php
    include __DIR__ . '/../../footer.html.php';
include __DIR__ . '/../../epilogue.html.php';
