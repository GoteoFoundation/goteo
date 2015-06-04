<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$dbg = false;
//if ($_SESSION['user']->id == 'root') $dbg = true;

if ($dbg) $ti = microtime(true);

$bodyClass = 'user-profile';
include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

$user = $vars['user'];
$worthcracy = Worth::getAll();
?>
<script type="text/javascript">

    jQuery(document).ready(function ($) {

        /* todo esto para cada lista de proyectos (flechitas navegacion) */
        <?php foreach ($vars['lists'] as $type=>$list) :
            if(array_empty($list)) continue; ?>
            $("#discover-group-<?php echo $type ?>-1").show();
            $("#navi-discover-group-<?php echo $type ?>-1").addClass('active');
        <?php endforeach; ?>

        $(".discover-arrow").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-discover-group-"+this.rev).removeClass('active');
            $(".discover-group-"+this.rev).hide();
            /* Poner acctive a este, mostrar este */
            $("#navi-discover-group-"+this.rel).addClass('active');
            $("#discover-group-"+this.rel).show();
        });

        $(".navi-discover-group").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-discover-group-"+this.rev).removeClass('active');
            $(".discover-group-"+this.rev).hide();
            /* Poner acctive a este, mostrar este */
            $("#navi-discover-group-"+this.rel).addClass('active');
            $("#discover-group-"+this.rel).show();
        });

    });
</script>

<?php echo View::get('user/widget/header.html.php', array('user'=>$user)) ?>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

<div id="main">

    <div class="center">

        <?php echo View::get('user/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?>

        <?php echo View::get('user/widget/about.html.php', array('user' => $user, 'projects' => $vars['projects'])) ?>

        <?php echo View::get('user/widget/social.html.php', array('user' => $user)) ?>


        <?php foreach ($vars['lists'] as $type=>$list) :
            if (array_empty($list))
                continue;
            ?>
            <div class="widget projects">
                <h2 class="title"><?php echo Text::get('profile-'.$type.'-header'); ?></h2>
                <?php foreach ($list as $group=>$projects) : ?>
                    <div class="discover-group discover-group-<?php echo $type ?>" id="discover-group-<?php echo $type ?>-<?php echo $group ?>">

                        <div class="discover-arrow-left">
                            <a class="discover-arrow" href="#<?php echo $type; ?>" rev="<?php echo $type ?>" rel="<?php echo $type.'-'.$projects['prev'] ?>">&nbsp;</a>
                        </div>

                        <?php foreach ($projects['items'] as $project) :
                            if ($type == 'my_projects')  {
                                echo View::get('project/widget/project.html.php', array('project' => $project));
                            } else {
                                echo View::get('project/widget/project.html.php', array('project' => $project, 'investor' => $user));
                            }
                        endforeach; ?>

                        <div class="discover-arrow-right">
                            <a class="discover-arrow" href="#<?php echo $type; ?>" rev="<?php echo $type ?>" rel="<?php echo $type.'-'.$projects['next'] ?>">&nbsp;</a>
                        </div>

                    </div>
                <?php endforeach; ?>


                <!-- carrusel de cuadritos -->
                <div class="navi-bar">
                    <ul class="navi">
                        <?php foreach (array_keys($list) as $group) : ?>
                        <li><a id="navi-discover-group-<?php echo $type.'-'.$group ?>" href="#<?php echo $type; ?>" rev="<?php echo $type ?>" rel="<?php echo "{$type}-{$group}" ?>" class="navi-discover-group navi-discover-group-<?php echo $type ?>"><?php echo $group ?></a></li>
                        <?php endforeach ?>
                    </ul>
                </div>

            </div>

        <?php endforeach; ?>

    </div>
    <div class="side">
        <?php if (!empty($_SESSION['user'])) echo View::get('user/widget/investors.html.php', $vars) ?>
        <?php echo View::get('user/widget/sharemates.html.php', $vars) ?>
    </div>

</div>

<?php
include __DIR__ . '/../footer.html.php';
include __DIR__ . '/../epilogue.html.php';

if ($dbg) {
    $tf = microtime(true);
    $tp = $tf - $ti;
    echo 'Tiempo de pintado = '.$tp.' segundos<br />';
}

