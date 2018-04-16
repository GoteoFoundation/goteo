<?php

use Goteo\Model\User\Interest,
    Goteo\Library\Text;

$user = $vars['user'];

$user->about = nl2br(Text::urlink($user->about));

$interests = Interest::getAll();
?>

<div class="widget user-about">


    <?php if (!empty($user->about)): ?>
    <div class="about">
        <h4><?php echo Text::get('profile-about-header'); ?></h4>
        <p><?php echo $user->about ?></p>
    </div>
    <?php endif ?>

    <?php if (!empty($user->interests)): ?>
    <div class="interests">
        <h4><?php echo Text::get('profile-interests-header'); ?></h4>
        <p><?php
        $c = 0;
        foreach ($user->interests as $interest) {
            if ($c > 0) echo ', ';
            echo $interests[$interest->interest];
            $c++;
        } ?></p>
    </div>
    <?php endif ?>

    <?php if (!empty($user->keywords)): ?>
    <div class="keywords">
        <h4><?php echo Text::get('profile-keywords-header'); ?></h4>
        <p><?php echo $user->keywords; ?></p>
    </div>
    <?php endif ?>

    <?php if (!empty($user->webs)): ?>
    <div class="webs">
        <h4><?php echo Text::get('profile-webs-header'); ?></h4>
        <ul>
            <?php foreach ($user->webs as $link): ?>
            <li><a href="<?php echo htmlspecialchars($link->url) ?>" target="_blank"><?php echo htmlspecialchars($link->url) ?></a></li>
            <?php endforeach ?>
        </ul>
    </div>
    <?php endif ?>

    <?php if (!empty($user->location)): ?>
     <div class="location">
        <h4><?php echo Text::get('profile-location-header'); ?></h4>
        <p><?php echo Text::GmapsLink($user->location); ?></p>
     </div>
    <?php endif ?>

    <?php if ($user->messageable): ?>
     <div class="message">
         <p><a href="/user/profile/<?php echo $user->id ?>/message"><?php echo Text::get('regular-send_message')?></a></p>
     </div>
    <?php endif ?>

</div>
