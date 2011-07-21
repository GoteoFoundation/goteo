<?php
use Goteo\Core\View,
    Goteo\Library\Text;
?>
<div class="widget user-supporters">
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
    <a class="more" href="/user/profile/<?php echo $this['user']->id ?>/investors"><?php echo Text::get('regular-see_more'); ?></a>
</div>
