<?php

use Goteo\Core\View,
    Goteo\Controller\Manage;

if (LANG != 'es') {
    header('Location: /manage?lang=es');
    exit;
}

$bodyClass = 'admin';

include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';
?>

        <div id="sub-header" style="margin-bottom: 10px;">
            <div class="breadcrumbs">Panel Gestor&iacute;a<?php // echo ADMIN_BCPATH; ?></div>
        </div>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

        <div id="main">

            <div class="admin-center">

            <a href="/manage/donors">Certificados</a>

<?php if (!empty($vars['folder']) && !empty($vars['file'])) :
        if ($vars['folder'] == 'base') {
            $path = 'manage/'.$vars['file'].'.html.php';
        } else {
            $path = 'manage/'.$vars['folder'].'/'.$vars['file'].'.html.php';
        }

            echo View::get($path, $vars);
       else :

        // Central pendientes
    ?>
        <div class="widget admin-home">
            <h3 class="title">Tareas pendientes</h3>
            <?php if (!empty($vars['tasks'])) : ?>
            <table>
                <?php foreach ($vars['tasks'] as $task) : ?>
                <tr>
                    <td><?php if (!empty($task->url)) { echo ' <a href="'.$task->url.'">[IR]</a>';} ?></td>
                    <td><?php echo $task->text; ?></td>
                    <td><?php if (empty($task->done)) { echo ' <a href="/manage/done/'.$task->id.'" onclick="return confirm(\'Seguro que esta tarea ya esta realizada?\')">[Dar por realizada]</a>';} ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else : ?>
            <p>No tienes tareas pendientes</p>
            <?php endif; ?>
        </div>

        <?php endif; ?>

            </div> <!-- fin center -->

        </div> <!-- fin main -->

<?php
    include __DIR__ . '/../footer.html.php';
include __DIR__ . '/../epilogue.html.php';
