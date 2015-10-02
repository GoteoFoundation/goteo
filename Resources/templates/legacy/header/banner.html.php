<?php
use Goteo\Library\Text;

$banner = $vars['banner'];

$metter_txt = Text::get('regular-banner-metter');
list($mreach, $mof, $mrest) = explode('-', $metter_txt);

if (empty($banner->project)) :
    if (!empty($banner->url)) echo '<a href="'.$banner->url.'" class="expand"></a>';
?>
<div class="shb-banner clearfix">
    <div class="title"><?php echo $banner->title ?></div>
    <div class="short-desc"><?php echo $banner->description ?></div>
</div>
<?php
else :
    $banner->title = $banner->project_name;
?>
<a href="/project/<?php echo $banner->project ?>" class="expand"></a>
<div class="shb-info clearfix">
    <h2><?php echo $banner->project_name ?></h2>
    <small><?php echo Text::get('regular-by') ?> <?php echo $banner->project_user_name ?></small>
    <div class="col-return clearfix">
        <h3><?php echo Text::get('project-rewards-social_reward-title') ?></h3>
        <p><?php echo current($banner->project_social_rewards)->reward ?></p>
        <ul>
            <?php $c = 1; foreach ($banner->project_social_rewards as $id => $reward) : ?>
            <li><img src="<?php echo SRC_URL ?>/view/css/icon/s/<?php echo $reward->icon ?>.png" alt="<?php echo $reward->icon ?>" title="<?php echo htmlspecialchars($reward->reward) ?>" /></li>
            <?php if ($c>4) break; else $c++; endforeach; ?>
        </ul>
        <div class="license"><?php foreach ($banner->project_social_rewards as $id => $reward) :
            if (empty($reward->license)) continue; ?>
            <img src="<?php echo SRC_URL ?>/view/css/license/<?php echo $reward->license ?>.png" alt="<?php echo $reward->license ?>" />
            <?php break; endforeach; ?>
        </div>
    </div>
    <ul class="financ-meter">
        <li><?php echo $mreach ?></li>
        <li class="reached"><?php echo \amount_format($banner->project_amount) ?></li>
        <li><?php echo $mof ?></li>
        <li class="optimun"><?php echo ($banner->project_amount >= $banner->project_mincost) ? \amount_format($banner->project_maxcost) : \amount_format($banner->project_mincost); ?></li>
        <?php if ($banner->project_days > 0) : ?>
        <li><?php echo $mrest ?></li>
        <li class="days"><?php echo $banner->project_days ?> <?php echo Text::get('regular-days') ?></li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
<?php if ($banner->image instanceof \Goteo\Model\Image) : ?><div class="shb-img"><img src="<?php echo $banner->image->getLink(700, 156, true) ?>" title="<?php echo htmlspecialchars($banner->title) ?>" alt="<?php echo htmlspecialchars($banner->title) ?>" /></div><?php endif; ?>
