<?php

use Goteo\Core\View,
    Goteo\Model\Patron,
    Goteo\Library\Text;

$user = $vars['user'];

?>
<div class="patron">
        <a class="expand" href="/user/profile/<?php echo htmlspecialchars($user->id) ?>"></a>
        <div class="box">
            <div class="avatar"><img src="<?php echo $user->avatar->getLink(112, 74, true); ?>" alt="<?php echo $user->name; ?>"/></div>
        </div>
        <div class="reco">
            <span class="name"><?php echo $user->name; ?></span><br />
            <?php echo Text::html('profile-patron-header', $user->num_patron_active) ?>
        </div>
</div>
