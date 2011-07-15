<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$bodyClass = 'user-profile';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$worthcracy = Worth::getAll();
?>
<script type="text/javascript">

    jQuery(document).ready(function ($) {

        /* Rolover sobre los cuadros de color */
        $("li").hover(
                function () { $(this).addClass('active') },
                function () { $(this).removeClass('active') }
        );

        /* todo esto para cada lista de proyectos (flechitas navegacion) */
        <?php foreach ($this['lists'] as $type=>$list) :
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
        <h2><img src="/image/<?php echo $user->avatar->id; ?>/75/75" /> <?php echo Text::get('profile-name-header'); ?> <br /><em><?php echo $user->name; ?></em></h2>
    </div>
</div>

<div id="main">

    <div class="center">

        <?php echo new View('view/user/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?>

        <?php echo new View('view/user/widget/about.html.php', array('user' => $user)) ?>

        <?php echo new View('view/user/widget/social.html.php', array('user' => $user)) ?>


        <?php foreach ($this['lists'] as $type=>$list) :
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
                            echo new View('view/project/widget/project.html.php', array('project' => $project));
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
        <div class="widget user-supporters">
                <h3 class="supertitle"><?php echo Text::get('profile-my_investors-header'); ?></h3>
                <div class="supporters">
                <ul>
                    <?php $c=1; // limitado a 6 cofinanciadores en el lateral
                    foreach ($this['investors'] as $user => $investor): ?>
                    <li><?php echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy)) ?></li>
                    <?php if ($c>5) break; else $c++;
                    endforeach ?>
                </ul>
                </div>
                <a class="more" href=""><?php echo Text::get('regular-see_more'); ?></a>
            </div>

            <div class="widget user-mates">
                <h3 class="supertitle"><?php echo Text::get('profile-sharing_interests-header'); ?></h3>
                <div class="users">
                    <ul>
                    <?php $c=1; // limitado a 6 sharemates en el lateral
                    foreach ($this['shares'] as $mate): ?>
                        <li>
                            <div class="user">
                                <div class="avatar"><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><img src="/image/<?php echo $mate->avatar->id ?>/43/43" /></a></div>
                                <h4><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><?php echo htmlspecialchars($mate->user) ?></a></h4>
                                <span class="projects"><?php echo Text::get('regular-projects'); ?> (<?php echo $mate->projects ?>)</span>
                                <span class="invests"><?php echo Text::get('regular-investing'); ?> (<?php echo $mate->invests ?>)</span>
                            </div>
                        </li>
                    <?php if ($c>5) break; else $c++;
                    endforeach ?>
                    </ul>
                </div>
                <a class="more" href=""><?php echo Text::get('regular-see_more'); ?></a>
            </div>
    </div>

</div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>