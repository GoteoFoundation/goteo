<?php $user = $this['user'] ?>
<?php if (isset($user->facebook) || isset($user->linkedin) || isset($user->twitter)): ?>
<div class="widget user-social">
    <h4 class="title"><?php echo Text::get('profile-social-header'); ?></h4>
    <ul>
        <?php if (isset($user->facebook)): ?>
        <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>"><?php echo Text::get('regular-facebook'); ?></a></li>
        <?php endif ?>
        <?php if (isset($user->twitter)): ?>
        <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>"><?php echo Text::get('regular-twitter'); ?></a></li>
        <?php endif ?>
        <?php if (isset($user->linkedin)): ?>
        <li class="linkedin"><a href="<?php echo htmlspecialchars($user->linkedin) ?>"><?php echo Text::get('regular-linkedin'); ?></a></li>
        <?php endif ?>
    </ul>                
</div>            
<?php endif ?>