<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest,
    Goteo\Core\Redirection;

$bodyClass = 'user-profile';
include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

$user = $vars['user'];
$worthcracy = Worth::getAll();

$categories = Interest::getAll($user->id);

if (empty($categories)) {
    throw new Redirection('/user/profile/' . $vars['user']->id);
}

$limit = empty($vars['category']) ? 6 : 20;

$shares = array();
foreach ($categories as $catId => $catName) {
    $gente = Interest::share($user->id, $catId, $limit);
    if (count($gente) == 0) continue;
    $shares[$catId] = $gente;
}

if (empty($shares)) {
    throw new Redirection('/user/profile/' . $vars['user']->id);
}

?>

<?php echo View::get('user/widget/header.html.php', array('user'=>$user)) ?>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

<div id="main">

    <div class="center">


       <!-- lista de categorías -->
        <div class="widget categorylist">
            <h3 class="title"><?php echo Text::get('profile-sharing_interests-header');?></h3>
			<!--
            <div class="filters">
                <span>Ver por:</span>
                <ul>
                    <li><a href="#" class="active">Por categorías</a></li>
                    <li class="separator">|</li>
                    <li><a href="#">Por tags</a></li>
                </ul>
            </div>
			-->
            <script type="text/javascript">
            function displayCategory(categoryId){
                $(".user-mates").css("display","none");
                $("#cat" + categoryId).fadeIn("slow");
                $(".active").removeClass('active');
                $("#catlist" + categoryId).addClass('active');
            }
            </script>
            <div class="list">
                <ul>
                    <?php foreach ($categories as $catId=>$catName) : if (count($shares[$catId]) == 0) continue; ?>
                    <li><a id="catlist<?php echo $catId ?>" href="/user/profile/<?php echo $vars['user']->id ?>/sharemates/<?php echo $catId ?>" <?php if (!empty($vars['category'])) : ?>onclick="displayCategory(<?php echo $catId ?>); return false;"<?php endif; ?> <?php if ($catId == $vars['category']) echo 'class="active"'?>><?php echo $catName ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <!-- fin lista de categorías -->

        <!-- detalle de categoría (cabecera de categoría) -->
        <?php foreach ($shares as $catId => $sharemates) :
            if (count($sharemates) == 0) continue;
            shuffle($sharemates);
            ?>
            <div class="widget user-mates" id="cat<?php echo $catId;?>" <?php if (!empty($vars['category']) && $catId != $vars['category']) echo 'style="display:none;"'?>>
                <h3 class="title"><?php echo $categories[$catId] ?></h3>
                <div class="users">
                    <ul>
                    <?php
                    $cnt = 1;
                    foreach ($sharemates as $mate) :
                        if (empty($vars['category']) && $cnt > 6) break;
                    ?>
                        <li>
                            <div class="user">
                                <a href="/user/<?php echo htmlspecialchars($mate->user) ?>" class="expand">&nbsp;</a>
                                <div class="avatar"><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><img src="<?php echo $mate->avatar->getLink(43, 43, true) ?>" /></a></div>
                                <h4><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><?php echo htmlspecialchars($mate->name) ?></a></h4>
                                <span class="projects"><?php echo Text::get('regular-projects'); ?> (<?php echo $mate->projects ?>)</span>
                                <span class="invests"><?php echo Text::get('regular-investing'); ?> (<?php echo $mate->invests ?>)</span><br/>
                                <span class="profile"><a href="/user/profile/<?php echo htmlspecialchars($mate->user) ?>"><?php echo Text::get('profile-widget-button'); ?></a> </span>
<!--                                <span class="contact"><a href="/user/profile/<?php echo htmlspecialchars($mate->user) ?>/message"><?php echo Text::get('regular-send_message'); ?></a></span> -->
                            </div>
                        </li>
                    <?php
                    $cnt ++;
                    endforeach; ?>
                    </ul>
                </div>
        <?php if (empty($vars['category'])) : ?>
            <a class="more" href="/user/profile/<?php echo $vars['user']->id ?>/sharemates/<?php echo $catId ?>"><?php echo Text::get('regular-see_more'); ?></a>
        <?php else : ?>
            <a class="more" href="/user/profile/<?php echo $vars['user']->id ?>/sharemates"><?php echo Text::get('regular-see_all'); ?></a>
        <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <!-- fin detalle de categoría (cabecera de categoría) -->

    </div>
    <div class="side">
        <?php if (!empty($_SESSION['user'])) echo View::get('user/widget/investors.html.php', $vars) ?>
        <?php echo View::get('user/widget/user.html.php', $vars) ?>
    </div>

</div>

<?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
