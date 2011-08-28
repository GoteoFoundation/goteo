<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$user = $this['user'];

$worthcracy = Worth::getAll();
$level = (int) $this['level'] ?: 4;


?>
<div class="supporter">

    <span class="avatar"><img src="/image/<?php echo $user->avatar; ?>/43/43/1" /></span>
    <?php if ($user->user != 'anonymous') : ?>
    <h<?php echo $level ?>><a href="/user/<?php echo htmlspecialchars($user->user) ?>"><?php echo $user->name; ?></a></h<?php echo $level ?>>
    <?php else : ?>
    <h<?php echo $level ?> class="aqua"><?php echo $user->name; ?></h<?php echo $level ?>>
    <?php endif; ?>

    <dl>

        <?php  if (isset($user->projects))  : ?>
        <dt class="projects"><?php echo Text::get('profile-invest_on-title'); ?></dt>
        <dd class="projects"><strong><?php echo $user->projects ?></strong> <?php echo Text::get('regular-projects'); ?></dd>
        <?php endif; ?>

        <dt class="worthcracy"><?php echo Text::get('profile-worthcracy-title'); ?></dt>
        <dd class="worthcracy">
            <?php if (isset($user->worth)) echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?>
        </dd>

        <dt class="amount"><?php echo Text::get('profile-worth-title'); ?></dt>
        <dd class="amount"><strong><?php echo number_format($user->amount) ?></strong> <span class="euro">&euro;</span></dd>

        <dt class="date"><?php echo Text::get('profile-last_worth-title'); ?></dt>
        <dd class="date"><?php echo $user->date; ?></dd>

    </dl>
</div>

