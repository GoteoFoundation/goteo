<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$cuantos = count($vars['investors']);
?>
<?php if($cuantos > 0){	?>
<div class="widget user-supporters">
    <h3 class="supertitle"><?php echo Text::get('profile-my_investors-header') . " ($cuantos)" ?></h3>
    <div class="supporters">
        <ul>
            <?php $c=1; // limitado a 6 cofinanciadores en el lateral
            foreach ($vars['investors'] as $user => $investor): ?>
            <li class="activable"><?php echo View::get('user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $vars['worthcracy'])) ?></li>
            <?php if ($c>5) break; else $c++;
            endforeach ?>
        </ul>
    </div>
    <a class="more" href="/user/profile/<?php echo $vars['user']->id ?>/investors"><?php echo Text::get('regular-see_more'); ?></a>
</div>
<?php }?>
