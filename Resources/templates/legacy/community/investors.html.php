<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Library\Worth;

// niveles de meritocracia
$worthcracy = Worth::getAll();

$investors = $vars['investors'];

// ordenarlos por cantidad
uasort($investors,
    function ($a, $b) {
        if ($a->amount == $b->amount) return 0;
        return ($a->amount > $b->amount) ? -1 : 1;
        }
    );


?>
<div class="widget user-supporters">
    <h3 class="supertitle"><?php echo Text::get('feed-side-top_ten') ?></h3>
    <div class="supporters">
        <ul>
            <?php $c=1; foreach ($investors as $user => $investor):
                if ($user == 'anonymous') continue; ?>
            <li class="activable"><?php echo View::get('user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy)) ?></li>
            <?php if ($c>=10) break; else $c++; endforeach; ?>
        </ul>
    </div>

    <div class="side-worthcracy">
    <?php include __DIR__ . '/../worth/base.html.php' ?>
    </div>
</div>
