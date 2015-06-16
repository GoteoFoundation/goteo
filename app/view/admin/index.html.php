<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\ACL,
    Goteo\Library\Feed,
    Goteo\Model\Node,
    Goteo\Controller\AdminController as Admin;

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


include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';
?>

        <div id="sub-header" style="margin-bottom: 10px;">
            <div class="breadcrumbs"><?php echo ADMIN_BCPATH; ?></div>
        </div>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

        <div id="main">

            <div class="admin-center">

            <div class="admin-menu">
                <?php foreach ($_SESSION['admin_menu'] as $sCode=>$section) : ?>
                <fieldset>
                    <legend><?php echo $section['label'] ?></legend>
                    <ul>
                    <?php foreach ($section['options'] as $oCode=>$option) :
                        echo '<li><a href="/admin/'.$oCode.'">'.$option['label'].'</a></li>';
                    endforeach; ?>
                    </ul>
                </fieldset>
                <?php endforeach; ?>
            </div>

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
            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            $feed = empty($_GET['feed']) ? 'all' : $_GET['feed'];
            $items = Feed::getAll($feed, 'admin', 50, $node);

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
                    <td><?php if (empty($task->done)) { echo ' <a href="/admin/done/'.$task->id.'" onclick="return confirm(\'Seguro que esta tarea ya esta realizada?\')">[Dar por realizada]</a>';} ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else : ?>
            <p>No tienes tareas pendientes</p>
            <?php endif; ?>
        </div>
    <?php
        // Lateral de acctividad reciente (solo admins y superadmines)

           if (isset($_SESSION['user']->roles['admin'])
            || isset($_SESSION['user']->roles['superadmin'])
            || isset($_SESSION['user']->roles['root'])) :
    ?>
            <div class="admin-side">
                <a name="feed"></a>
                <div class="widget feed">
					<script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $('.scroll-pane').jScrollPane({showArrows: true});

                        $('.hov').hover(
                          function () {
                            $(this).addClass($(this).attr('rel'));
                          },
                          function () {
                            $(this).removeClass($(this).attr('rel'));
                          }
                        );

                    });
                    </script>
                    <h3>actividad reciente</h3>
                    Ver Feeds por:

                    <p class="categories">
                        <?php foreach (Feed::$admin_types as $id=>$cat) : ?>
                        <a href="/admin/recent/?feed=<?php echo $id ?>#feed" <?php echo ($feed == $id) ? 'class="'.$cat['color'].'"': 'class="hov" rel="'.$cat['color'].'"' ?>><?php echo $cat['label'] ?></a>
                        <?php endforeach; ?>
                    </p>

                    <div class="scroll-pane">
                        <?php foreach ($items as $item) :
                            $odd = !$odd ? true : false;
                            ?>
                        <div class="subitem<?php if ($odd) echo ' odd';?>">
                           <span class="datepub"><?php echo Text::get('feed-timeago', $item->timeago); ?></span>
                           <div class="content-pub"><?php echo $item->html; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <a href="/admin/recent/<?php echo isset($_GET['feed']) ? '?feed='.$_GET['feed'] : ''; ?>" style="margin-top:10px;float:right;text-transform:uppercase">Ver más</a>

                </div>
            </div>


        <?php
                endif;


            endif;
            ?>

            </div> <!-- fin center -->

        </div> <!-- fin main -->

<?php
    include __DIR__ . '/../footer.html.php';
include __DIR__ . '/../epilogue.html.php';
