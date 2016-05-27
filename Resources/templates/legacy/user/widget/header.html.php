<?php

use Goteo\Library\Text;

$user = $vars['user'];
?>
<div id="sub-header">
    <div>
        <h2><a href="/user/<?php echo $user->id; ?>"><img height="56" width="56" src="<?php echo $user->avatar->getLink(56, 56, true); ?>" /></a> <?php echo Text::get('profile-name-header'); ?> <br /><em><?php echo $user->name; ?></em></h2>
    </div>
</div>
