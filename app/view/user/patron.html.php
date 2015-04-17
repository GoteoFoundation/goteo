<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$bodyClass = 'home patron';
include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

$user = $vars['user'];
$recos = $vars['recos'];
shuffle($recos);
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

<div id="sub-header">
    <div>
        <h2><span class="greenblue"><?php echo $user->name ?></span><br /><?php echo Text::html('profile-patron-header', count($recos)) ?></h2>

        <?php if (!empty($user->node) && $user->node != \GOTEO_NODE) : ?>
        <div class="nodemark"><a class="node-jump" href="<?php echo $user->nodeData->url ?>" ><img src ="/nodesys/<?php echo $user->node ?>/sello.png" alt="<?php echo htmlspecialchars($user->nodeData->name) ?>" title="Nodo <?php echo htmlspecialchars($user->nodeData->name) ?>"/></a></div>
        <?php endif; ?>
    </div>

</div>

<?php if(isset($_SESSION['messages'])) { include __DIR__ . '/../header/message.html.php'; } ?>

<div id="main">

    <div class="patron-profile">
        <div class="avatar">
            <img src="<?php echo $user->avatar->getLink(210, 138, true); ?>" alt="<?php echo $user->name ?>"/><br />
            <!-- enlaces sociales (iconitos como footer) -->
            <ul>
                <?php if (!empty($user->facebook)): ?>
                <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>" target="_blank">F</a></li>
                <?php endif ?>
                <?php if (!empty($user->google)): ?>
                <li class="google"><a href="<?php echo htmlspecialchars($user->google) ?>" target="_blank">G</a></li>
                <?php endif ?>
                <?php if (!empty($user->twitter)): ?>
                <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>" target="_blank">T</a></li>
                <?php endif ?>
                <?php if (!empty($user->identica)): ?>
                <li class="identica"><a href="<?php echo htmlspecialchars($user->identica) ?>" target="_blank">I</a></li>
                <?php endif ?>
                <?php if (!empty($user->linkedin)): ?>
                <li class="linkedin"><a href="<?php echo htmlspecialchars($user->linkedin) ?>" target="_blank">L</a></li>
                <?php endif ?>
            </ul>
        </div>
        <div class="info">
            <!-- Nombre y texto presentación -->
            <p><strong><?php echo $user->name ?></strong> <?php echo $user->about ?></p>
            <!-- 2 webs -->
            <ul>
                <?php $c=0; foreach ($user->webs as $link): ?>
                <li><a href="<?php echo htmlspecialchars($link->url) ?>" target="_blank"><?php echo htmlspecialchars($link->url) ?></a></li>
                <?php $c++; if ($c>=2) break; endforeach ?>
            </ul>
        </div>
    </div>
    <br clear="all" />

    <div class="widget projects">

    <?php foreach ($recos as $reco) :
            echo View::get('project/widget/project.html.php', array(
                'project' => $reco->projectData,
                'balloon' => '<h4>' . htmlspecialchars($reco->title) . '</h4>' .
                             '<blockquote>' . $reco->patron_description . '</blockquote>',
                'investor' => $user
            ));
    endforeach ?>

    </div>

    <?php foreach ($vars['lists'] as $type=>$list) :
        if (\array_empty($list))
            continue;
        ?>
        <div class="widget projects patron_invests">
            <h2 class="title"><?php echo Text::get('profile_patron-'.$type.'-header'); ?></h2>
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

<?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
