<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$bodyClass = 'user-profile';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$worthcracy = Worth::getAll();
?>
<div id="sub-header">
    <div>
        <h2><img src="/image/<?php echo $user->avatar->id; ?>/75/75" /> <?php echo Text::get('profile-name-header'); ?> <br /><em><?php echo $user->name; ?></em></h2>
    </div>
</div>

<div id="main">

    <div class="center">
        <div class="widget sharemates">
            <h3 class="supertitle"><?php echo Text::get('profile-sharing_interests-header'); ?></h3>
            <div class="users">
                <ul>
                <?php $c=1; // limitado a 6 sharemates en el lateral
                foreach ($this['shares'] as $mate): ?>
                    <li>
                        <div class="user">
                            <div class="avatar"><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><img src="/image/<?php echo $mate->avatar->id ?>/43/43" /></a></div>
                            <h4><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><?php echo htmlspecialchars($mate->user) ?></a></h4>
                            <span class="projects"><?php echo Text::get('regular-projects'); ?> (<?php echo $mate->projects ?>)</span>
                            <span class="invests"><?php echo Text::get('regular-investing'); ?> (<?php echo $mate->invests ?>)</span>
                        </div>
                    </li>
                <?php if ($c>5) break; else $c++;
                endforeach ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="side">
        <?php echo new View('view/user/widget/investors.html.php', $this) ?>
        <?php echo new View('view/user/widget/user.html.php', $this) ?>
    </div>

</div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>