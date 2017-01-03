<?php

use Goteo\Core\View;

$user = $this->user;
$worthcracy = $this->worthcracy;

$this->layout('layout', [
    'bodyClass' => 'user-profile',
    ]);

$this->section('content');
?>

<?= View::get('user/widget/header.html.php', array('user' => $user)) ?>

<div id="main">

    <div class="center">

        <?= View::get('user/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?>

        <?= View::get('user/widget/about.html.php', array('user' => $user, 'projects' => $this->projects)) ?>

        <?= View::get('user/widget/social.html.php', array('user' => $user)) ?>


        <?php foreach ($this->lists as $type => $list) :
            if (array_empty($list))
                continue;
            ?>
            <div class="widget projects">
                <h2 class="title"><?= $this->text('profile-'.$type.'-header') ?></h2>
                <?php foreach ($list as $group => $projects) : ?>
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
    <div class="side">
        <?php if ($this->is_logged()) echo View::get('user/widget/investors.html.php', $this->vars) ?>
        <?= View::get('user/widget/sharemates.html.php', $this->vars) ?>
    </div>

</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

    jQuery(document).ready(function ($) {

        /* todo esto para cada lista de proyectos (flechitas navegacion) */
        <?php foreach ($this->lists as $type => $list) :
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
// @license-end
</script>

<?php $this->append() ?>
