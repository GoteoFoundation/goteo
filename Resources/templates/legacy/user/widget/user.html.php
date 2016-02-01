<?php

use Goteo\Library\Text;

$user = $vars['user'];
$level = (int) $vars['level'] ?: 3;

// autodetección de enlaces en el texto
$user->about = nl2br(Text::urlink($user->about));
?>

<div class="widget user collapsable">

    <h<?php echo $level ?> class="supertitle"><?php echo Text::get('profile-widget-user-header'); ?></h<?php echo $level ?>>

    <h<?php echo $level + 1 ?> class="title">
    <?php echo htmlspecialchars($user->name) ?></h<?php echo $level + 1 ?>>

    <div class="image">
        <?php if (!empty($user->avatar)): ?><img alt="<?php echo htmlspecialchars($user->name) ?>" src="<?php echo $user->avatar->getLink(80, 80, true); ?>" /><?php endif ?>
    </div>

    <?php if ($user->about): ?>
    <blockquote class="about">
    <?php echo $user->about ?>
    </blockquote>
    <?php endif ?>

    <dl>

        <?php if ($user->location): ?>
        <dt class="location"><?php echo Text::get('profile-location-header'); ?></dt>
        <dd class="location"><?php echo Text::GmapsLink($user->location); ?></dd>
        <?php endif ?>

        <?php if (!empty($user->webs)): ?>
        <dt class="links"><?php echo Text::get('profile-webs-header'); ?></dt>
        <dd class="links">
            <ul>
                <?php foreach ($user->webs as $link): ?>
                <li><a href="<?php echo htmlspecialchars($link->url) ?>"><?php echo substr(htmlspecialchars($link->url),0,42); ?></a></li>
                <?php endforeach ?>
            </ul>
        </dd>
        <?php endif ?>
        <?php if (!empty($user->facebook) || !empty($user->twitter)): ?>
        <dt class="user-social"><?php echo Text::get('profile-fields-social-title'); ?></dt>
        <dd class="user-social">
            <ul>
                <?php if (!empty($user->twitter)): ?>
                <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>"><?php echo Text::get('regular-twitter'); ?></a></li>
                <?php endif ?>
                <?php if (!empty($user->facebook)): ?>
                <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>"><?php echo Text::get('regular-facebook'); ?></a></li>
                <?php endif ?>
            </ul>
        </dd>
        <?php endif ?>

        <dt class="message"><?php echo Text::get('regular-send_message')?></dt>
        <dd class="message"><a href="/user/profile/<?php echo htmlspecialchars($user->id) ?>/message"><?php echo Text::get('regular-send_message')?></a></dd>


    </dl>

    <a class="button aqua profile" href="/user/<?php echo $user->id; ?>"><?php echo Text::get('profile-widget-button'); ?></a>

</div>

