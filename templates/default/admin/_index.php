<?php

use Goteo\Core\View,
    Goteo\Controller\AdminController as Admin;

// piñones usuarios
$allowed = Admin::$supervisors[$_SESSION['user']->id];

if (isset($allowed) && !empty($vars['folder']) && !in_array($vars['folder'], $allowed)) {
    header('Location: /admin/');
    exit;
}

$this->layout('layout', [
    'bodyClass' => 'admin',
    'jsreq_autocomplete' => true,
    ]);
?>

<?php $this->section('sub-header') ?>
<?= $this->insert('admin/partials/breadcrumb') ?>
<?php $this->replace() ?>

<?php $this->section('content') ?>

        <div id="main">

            <div class="admin-center">

            <?= $this->insert('admin/partials/menu') ?>

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
                </ul>
            </div>
            <?php endif; ?>


<?php if (!empty($vars['folder']) && !empty($vars['file'])) :
        if ($vars['folder'] == 'base') {
            $path = 'admin/'.$vars['file'].'.html.php';
        } else {
            $path = 'admin/'.$vars['folder'].'/'.$vars['file'].'.html.php';
        }

            echo View::get($path, $vars);
       else :

            /* PORTADA ADMIN */


        // Lateral de acctividad reciente (solo admins y superadmines)

           if (isset($_SESSION['user']->roles['admin'])
            || isset($_SESSION['user']->roles['superadmin'])
            || isset($_SESSION['user']->roles['root'])) :
    ?>

            <?php $this->supply('admin-aside') ?>

        <?php
                endif;


            endif;
            ?>

            </div> <!-- fin center -->

        </div> <!-- fin main -->

<?php $this->replace() ?>
