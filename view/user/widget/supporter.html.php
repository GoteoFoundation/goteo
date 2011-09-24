<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Worth;

$user = $this['user'];

$worthcracy = Worth::getAll();
?>
<div class="supporter">

    <span class="avatar"><img src="/image/<?php echo $user->avatar; ?>/43/43/1" /></span>
    <?php if ($user->user != 'anonymous') : ?>
    <h4><a href="/user/<?php echo htmlspecialchars($user->user) ?>"><?php echo $user->name; ?></a></h4>
    <?php else : ?>
    <h4 class="aqua"><?php echo Text::recorta($user->name,40); ?></h4>
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

