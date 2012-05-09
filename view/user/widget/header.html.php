<?php

use Goteo\Library\Text;

$user = $this['user'];
?>
<div id="sub-header">
    <div>
        <h2><a href="/user/<?php echo $user->id; ?>"><img src="<?php echo $user->avatar->getLink(56, 56, true); ?>" /></a> <?php echo Text::get('profile-name-header'); ?> <br /><em><?php echo $user->name; ?></em></h2>
    </div>

    <?php /* if (!empty($user->node) && $user->node != \NODE_ID) : ?>
    <div class="nodemark"><a class="node-jump" href="<?php echo $user->nodeData->url ?>" >NODO: <?php echo $user->nodeData->name ?></a></div>
    <?php endif; */ ?>
</div>
