<?php

use Goteo\Core\View;

$user = $this->user;
$shares = $this->shares;
$categories = $this->categories;

$this->layout('layout', [
    'bodyClass' => 'user-profile',
    ]);

$this->section('content');

?>

<?php echo View::get('user/widget/header.html.php', array('user' => $user)) ?>

<div id="main">

    <div class="center">


       <!-- lista de categorías -->
        <div class="widget categorylist">
            <h3 class="title"><?= $this->text('profile-sharing_interests-header');?></h3>

            <div class="list">
                <ul>
                    <?php foreach ($categories as $catId => $catName) : if (count($shares[$catId]) == 0) continue; ?>
                    <li><a id="catlist<?= $catId ?>" href="/user/profile/<?= $user->id ?>/sharemates/<?= $catId ?>" <?php if (!empty($this->category)) : ?>onclick="displayCategory(<?= $catId ?>); return false;"<?php endif; ?> <?php if ($catId == $this->category) echo 'class="active"'?>><?= $catName ?></a></li>
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
            <div class="widget user-mates" id="cat<?= $catId;?>" <?php if (!empty($this->category) && $catId != $this->category) echo 'style="display:none;"'?>>
                <h3 class="title"><?= $categories[$catId] ?></h3>
                <div class="users">
                    <ul>
                    <?php
                    $cnt = 1;
                    foreach ($sharemates as $mate) :
                        if (empty($this->category) && $cnt > 6) break;
                    ?>
                        <li>
                            <div class="user">
                                <a href="/user/<?= $mate->user ?>" class="expand">&nbsp;</a>
                                <div class="avatar"><a href="/user/<?= $mate->user ?>"><img src="<?= $mate->avatar->getLink(43, 43, true) ?>" /></a></div>
                                <h4><a href="/user/<?= $mate->user ?>"><?= $mate->name ?></a></h4>
                                <span class="projects"><?= $this->text('regular-projects') ?> (<?= $mate->projects ?>)</span>
                                <span class="invests"><?= $this->text('regular-investing') ?> (<?= $mate->invests ?>)</span><br/>
                                <span class="profile"><a href="/user/profile/<?= $mate->user ?>"><?= $this->text('profile-widget-button') ?></a> </span>
<!--                                <span class="contact"><a href="/user/profile/<?= $mate->user ?>/message"><?= $this->text('regular-send_message') ?></a></span> -->
                            </div>
                        </li>
                    <?php
                    $cnt ++;
                    endforeach; ?>
                    </ul>
                </div>
        <?php if (empty($this->category)) : ?>
            <a class="more" href="/user/profile/<?= $user->id ?>/sharemates/<?= $catId ?>"><?= $this->text('regular-see_more') ?></a>
        <?php else : ?>
            <a class="more" href="/user/profile/<?= $user->id ?>/sharemates"><?= $this->text('regular-see_all') ?></a>
        <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <!-- fin detalle de categoría (cabecera de categoría) -->

    </div>
    <div class="side">
        <?php if ($this->is_logged()) echo View::get('user/widget/investors.html.php', $this->vars) ?>
        <?php echo View::get('user/widget/user.html.php', $this->vars) ?>
    </div>

</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
function displayCategory(categoryId){
    $(".user-mates").css("display","none");
    $("#cat" + categoryId).fadeIn("slow");
    $(".active").removeClass('active');
    $("#catlist" + categoryId).addClass('active');
}
// @license-end
</script>

<?php $this->append() ?>
