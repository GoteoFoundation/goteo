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
        <div class="widget investors">
            <h3 class="supertitle"><?php echo Text::get('profile-my_investors-header'); ?></h3>
            <div class="supporters">
                <ul>
                    <?php $c=1; // limitado a 6 cofinanciadores en el lateral
                    foreach ($this['investors'] as $user => $investor): ?>
                    <li class="activable"><?php echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $this['worthcracy'])) ?></li>
                    <?php if ($c>5) break; else $c++;
                    endforeach ?>
                </ul>
            </div>
            <!-- paginacion -->
        </div>
    </div>
    <div class="side">
        <?php echo new View('view/user/widget/sharemates.html.php', $this) ?>
        <?php echo new View('view/user/widget/user.html.php', $this) ?>
    </div>

</div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>