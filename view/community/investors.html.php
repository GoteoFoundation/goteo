<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$investors = $this['investors'];

// ordenarlos por cantidad
uasort($investors,
    function ($a, $b) {
        if ($a->amount == $b->amount) return 0;
        return ($a->amount > $b->amount) ? -1 : 1;
        }
    );


?>
<div class="widget user-supporters">
    <h3 class="supertitle">Top Ten cofinanciadores</h3>
    <div class="supporters">
        <ul>
            <?php $c=1; foreach ($investors as $user => $investor):
                if ($user == 'anonymous') continue; ?>
            <li class="activable"><?php echo new View('view/user/widget/supporter.html.php', array('user' => $investor)) ?></li>
            <?php if ($c>=10) break; else $c++; endforeach; ?>
        </ul>
    </div>
    
    <div class="side-worthcracy">
    <?php include 'view/worth/base.html.php' ?>
    </div>
</div>