<?php

$user = $this['user'];
$worthcracy = $this['worthcracy'];

?>
<div style="display:block;margin: 20px;">
    <img src="/image/<?php echo $user->avatar->id; ?>/50/50" />
    <a href="/user/<?php echo $user->user; ?>"><?php echo $user->name; ?></a><br />
        Cofinancia: <?php echo $user->projects; ?> proyectos<br />
        <?php echo $worthcracy[$user->worth]->name; ?><br />
        Aporta: <span class="amount"><?php echo number_format($user->amount); ?> &euro;</span><br />
        <span class="date"><?php echo $user->date; ?></span>
</div>

