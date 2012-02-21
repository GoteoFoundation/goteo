<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$user = $this['user'];
?>
<div style="float: left; margin-right: 20px; border: 1px solid black; padding: 10px;">
	<div class="patron">
		<a href="/discover/patron/<?php echo htmlspecialchars($user->id) ?>">
            <span class="avatar"><img src="<?php echo $user->avatar->getLink(43, 43, true); ?>" /></span>
            <h4><?php echo $user->name; ?></h4>
        </a>
	</div>
</div>
