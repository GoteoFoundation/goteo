<?php $user = $this['user'] ?>
<?php if (isset($user->facebook) || isset($user->linkedin) || isset($user->twitter)): ?>
<div class="widget user-social">
    <h4 class="title">Social</h4>
    <ul>
        <?php if (isset($user->facebook)): ?>
        <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>">Facebook</a></li>
        <?php endif ?>
        <?php if (isset($user->twitter)): ?>
        <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>">Twitter</a></li>
        <?php endif ?>
        <?php if (isset($user->linkedin)): ?>
        <li class="linkedin"><a href="<?php echo htmlspecialchars($user->linkedin) ?>">LinkedIn</a></li>
        <?php endif ?>
    </ul>                
</div>            
<?php endif ?>