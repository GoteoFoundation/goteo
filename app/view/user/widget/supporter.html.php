<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$user = $vars['user'];
$worthcracy = $vars['worthcracy'];
?>
<div class="supporterContainer">
	<?php if ($user->campaign) : ?>
	<a class="expand" href="/call/<?php echo $user->call ?>" target="_blank">&nbsp;</a>
	<?php elseif ($user->user != 'anonymous') : ?>
	<a class="expand" href="/user/<?php echo htmlspecialchars($user->user) ?>">&nbsp;</a>
	<?php endif; ?>
    <?php if ($user->droped || $user->campaign) : ?>
    <div class="droped">&nbsp;</div>
    <?php endif; ?>
	<div class="supporter">
		<span class="avatar"><img src="<?php echo $user->avatar->getLink(43, 43, true); ?>" /></span>
	    <?php if ($user->user == 'anonymous') : ?>
	    <h4><?php echo $user->name; ?></h4>
	    <?php else : ?>
	    <h4 class="aqua"<?php if ($user->campaign) echo 'style="color: #96238F;"'; ?>><?php echo Text::recorta($user->name,40); ?></h4>
	    <?php endif; ?>
	    <dl>
	        <?php  if (isset($user->projects))  : ?>
	        <dt class="projects"><?php echo Text::get('profile-invest_on-title'); ?></dt>
	        <dd class="projects"><strong><?php echo $user->projects ?></strong> <?php echo Text::get('regular-projects'); ?></dd>
	        <?php endif; ?>

	        <dt class="worthcracy"><?php echo Text::get('profile-worthcracy-title'); ?></dt>
	        <dd class="worthcracy">
	            <?php if (isset($user->worth)) echo View::get('worth/base.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?>
	        </dd>

	        <dt class="amount"><?php echo Text::get('profile-worth-title'); ?></dt>
	        <dd class="amount<?php if ($user->campaign) echo ' campaign'; ?>"><strong><?php echo \amount_format($user->amount) ?></strong></dd>

	        <dt class="date"><?php echo Text::get('profile-last_worth-title'); ?></dt>
	        <dd class="date"><?php echo $user->date; ?></dd>
	    </dl>
	</div>
</div>
