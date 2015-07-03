<?php

use Goteo\Core\View;

$user = $this->user;
$recommended = $this->recommended;
shuffle($recommended);

$this->layout('layout', [
    'bodyClass' => 'home patron',
    ]);

$this->section('content');
?>

<div id="sub-header">
    <div>
        <h2><span class="greenblue"><?= $user->name ?></span><br /><?= $this->text_html('profile-patron-header', count($recommended)) ?></h2>

        <?php if (!empty($user->node) && $user->node != \GOTEO_NODE) : ?>
        <div class="nodemark"><a class="node-jump" href="<?= $user->nodeData->url ?>" ><img src ="/nodesys/<?= $user->node ?>/sello.png" alt="<?= $user->nodeData->name ?>" title="Nodo <?= $user->nodeData->name ?>"/></a></div>
        <?php endif; ?>
    </div>

</div>

<div id="main">

    <div class="patron-profile">
        <div class="avatar">
            <img src="<?= $user->avatar->getLink(210, 138, true) ?>" alt="<?= $user->name ?>"/><br />
            <!-- enlaces sociales (iconitos como footer) -->
            <ul>
                <?php if (!empty($user->facebook)): ?>
                <li class="facebook"><a href="<?= $user->facebook ?>" target="_blank">F</a></li>
                <?php endif ?>
                <?php if (!empty($user->google)): ?>
                <li class="google"><a href="<?= $user->google ?>" target="_blank">G</a></li>
                <?php endif ?>
                <?php if (!empty($user->twitter)): ?>
                <li class="twitter"><a href="<?= $user->twitter ?>" target="_blank">T</a></li>
                <?php endif ?>
                <?php if (!empty($user->identica)): ?>
                <li class="identica"><a href="<?= $user->identica ?>" target="_blank">I</a></li>
                <?php endif ?>
                <?php if (!empty($user->linkedin)): ?>
                <li class="linkedin"><a href="<?= $user->linkedin ?>" target="_blank">L</a></li>
                <?php endif ?>
            </ul>
        </div>
        <div class="info">
            <!-- Nombre y texto presentación -->
            <p><strong><?= $user->name ?></strong> <?= $user->about ?></p>
            <!-- 2 webs -->
            <ul>
                <?php $c=0; foreach ($user->webs as $link): ?>
                <li><a href="<?= $link->url ?>" target="_blank"><?= $link->url ?></a></li>
                <?php $c++; if ($c>=2) break; endforeach ?>
            </ul>
        </div>
    </div>
    <br clear="all" />

    <div class="widget projects">

    <?php foreach ($recommended as $reco) :
            echo View::get('project/widget/project.html.php', array(
                'project' => $reco->projectData,
                'balloon' => '<h4>' . $reco->title . '</h4>' .
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
            <h2 class="title"><?= $this->text('profile_patron-'.$type.'-header') ?></h2>
            <?php foreach ($list as $group=>$projects) : ?>
                <div class="discover-group discover-group-<?= $type ?>" id="discover-group-<?= $type ?>-<?= $group ?>">

                    <div class="discover-arrow-left">
                        <a class="discover-arrow" href="#<?= $type ?>" rev="<?= $type ?>" rel="<?= $type.'-'.$projects['prev'] ?>">&nbsp;</a>
                    </div>

                    <?php foreach ($projects['items'] as $project) :
                        if ($type == 'my_projects')  {
                            echo View::get('project/widget/project.html.php', array('project' => $project));
                        } else {
                            echo View::get('project/widget/project.html.php', array('project' => $project, 'investor' => $user));
                        }
                    endforeach; ?>

                    <div class="discover-arrow-right">
                        <a class="discover-arrow" href="#<?= $type ?>" rev="<?= $type ?>" rel="<?= $type.'-'.$projects['next'] ?>">&nbsp;</a>
                    </div>

                </div>
            <?php endforeach; ?>


            <!-- carrusel de cuadritos -->
            <div class="navi-bar">
                <ul class="navi">
                    <?php foreach (array_keys($list) as $group) : ?>
                    <li><a id="navi-discover-group-<?= $type.'-'.$group ?>" href="#<?= $type ?>" rev="<?= $type ?>" rel="<?= "{$type}-{$group}" ?>" class="navi-discover-group navi-discover-group-<?= $type ?>"><?= $group ?></a></li>
                    <?php endforeach ?>
                </ul>
            </div>

        </div>
        <?php endforeach; ?>


</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">

    jQuery(document).ready(function ($) {

        /* todo esto para cada lista de proyectos (flechitas navegacion) */
        <?php foreach ($vars['lists'] as $type=>$list) :
            if(array_empty($list)) continue; ?>
            $("#discover-group-<?= $type ?>-1").show();
            $("#navi-discover-group-<?= $type ?>-1").addClass('active');
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

<?php $this->append() ?>
