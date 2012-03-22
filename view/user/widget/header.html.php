<?php

use Goteo\Library\Text;

$user = $this['user'];
?>
<div id="sub-header">
    <div>
        <h2><a href="/user/<?php echo $user->id; ?>"><img src="<?php echo $user->avatar->getLink(56, 56, true); ?>" /></a> <?php echo Text::get('profile-name-header'); ?> <br /><em><?php echo $user->name; ?></em></h2>
    </div>

    <?php if (!empty($user->node) && $user->node != \GOTEO_NODE) : ?>
    <div class="nodemark">NODO: <?php echo $user->node ?></div>
    <?php endif; ?>
</div>
