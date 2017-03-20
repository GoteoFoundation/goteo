<?php

$banner = $this->banner;

$metter_txt = $this->text('regular-banner-metter');
list($mreach, $mof, $mrest) = explode('-', $metter_txt);

if (empty($banner->project)):
    if (!empty($banner->url))  echo '<a href="'.$banner->url.'" class="expand"></a>';
    ?>
    <div class="shb-banner clearfix">
        <div class="title"><?= $banner->title ?></div>
        <div class="short-desc"><?= $banner->description ?></div>
    </div>
    <?php

    else:
        $banner->title = $banner->project_name;
        $project_social_rewards = $banner->project_social_rewards;
        if(!is_array($project_social_rewards)) $project_social_rewards = [];
    ?>
    <a href="/project/<?= $banner->project ?>" class="expand"></a>
    <div class="shb-info clearfix">
        <h2><?= $banner->project_name ?></h2>
        <small><?= $this->text('regular-by') ?> <?= $banner->project_user_name ?></small>
        <div class="col-return clearfix">
            <?php if($banner->project_social_commitment): ?>
                <h3><?= $this->text('project-social-commitment-title') ?></h3>
                <div>
                <img class="img-social-commitment" width="35" src="<?= $banner->social_commitmentData->image->getLink(60, 60, false) ?>" alt="<?= $banner->social_commitmentData->name ?>" title="<?= $banner->social_commitmentData->name ?>" />
                <span><?= $banner->social_commitmentData->name ?></span>
                </div>
            <?php 
                else:
            ?>
                <h3><?= $this->text('project-rewards-social_reward-title') ?></h3>
                <p><?= current($project_social_rewards)->reward ?></p>
                <ul>
            <?php
            $c = 1;
            foreach ($project_social_rewards as $id => $reward) :
            ?>
                <li><img src="<?= SRC_URL ?>/view/css/icon/s/<?= $reward->icon ?>.png" alt="<?= $reward->icon ?>" title="<?= $this->ee($reward->reward) ?>" /></li>
            <?php
                if ($c>4) break;
                else      $c++;
            endforeach;
            ?>
            </ul>
            <div class="license">
            <?php
            foreach ($project_social_rewards as $id => $reward):
                if (empty($reward->license)) continue;
            ?>
                <img src="<?= SRC_URL ?>/view/css/license/<?= $reward->license ?>.png" alt="<?= $reward->license ?>" />
            <?php
                break;
            endforeach;
            ?>
            </div>
        <?php endif; ?>
        </div>
        <ul class="financ-meter">
            <li><?= $mreach ?></li>
            <li class="reached"><?= \amount_format($banner->project_amount) ?></li>
            <li><?= $mof ?></li>
            <li class="optimun"><?= ($banner->project_amount >= $banner->project_mincost) ? \amount_format($banner->project_maxcost) : \amount_format($banner->project_mincost); ?></li>
        <?php if ($banner->project_days > 0): ?>
            <li><?= $mrest ?></li>
            <li class="days"><?= $banner->project_days ?> <?= $this->text('regular-days') ?></li>
        <?php endif ?>
        </ul>
    </div>
<?php endif ?>

<?php if ($banner->image instanceof \Goteo\Model\Image): ?>
<div class="shb-img"><img src="<?= $banner->image->getLink(700, 156, true) ?>" title="<?= $this->ee($banner->title) ?>" alt="<?= $this->ee($banner->title) ?>" /></div>
<?php endif ?>
