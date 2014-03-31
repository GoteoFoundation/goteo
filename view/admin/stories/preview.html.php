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
?>

        <div id="sub-header">
            <div class="breadcrumbs"><?php echo ADMIN_BCPATH; ?></div>
        </div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

        <div id="main">

            <div class="admin-center">


            <?php if (isset($_SESSION['user']->roles['superadmin'])) : ?>
            <div class="widget board">
                <ul>
                    <li><a href="/admin/projects">Proyectos</a></li>
                    <li><a href="/admin/users">Usuarios</a></li>
                    <li><a href="/admin/accounts">Aportes</a></li>
                    <li><a href="/admin/calls">Convocatorias</a></li>
                    <li><a href="/admin/tasks">Tareas</a></li>
                    <li><a href="/admin/nodes">Nodos</a></li>
                    <li><a href="/admin/reports">Informes</a></li>
                    <li><a href="/admin/newsletter">Boletin</a></li>
                    <li><a href="/admin/locations">GeoLoc.</a></li>
                </ul>
            </div>
            <?php endif; ?>


            <?php include 'view/home/stories.html.php'; ?>

            </div> <!-- fin center -->

        </div> <!-- fin main -->

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
