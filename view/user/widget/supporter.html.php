<?php

use Goteo\Core\View;

$user = $this['user'];
$worthcracy = $this['worthcracy'];
$level = (int) $this['level'] ?: 4;


?>
<div class="supporter">
    
    <span class="avatar"><img src="/image/<?php echo $user->avatar->id; ?>/50/50" /></span>
    <h<?php echo $level ?>><a href="/user/<?php echo htmlspecialchars($user->user) ?>"><?php echo $user->name; ?></a></h<?php echo $level ?>>
    
    <dl>
        
        <dt class="projects"><?php echo Texg::get('profile-invest_on-title'); ?></dt>
        <dd class="projects"><strong><?php echo $user->projects ?></strong> <?php echo Texg::get('regular-projects'); ?></dd>
        
        <dt class="worthcracy"><?php echo Texg::get('profile-worthcracy-title'); ?></dt>
        <dd class="worthcracy">            
            <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?> 
        </dd>
        
        <dt class="amount"><?php echo Texg::get('profile-worth-title'); ?></dt>
        <dd class="amount"><strong><?php echo number_format($user->amount) ?></strong> <span class="euro">&euro;</span></dd>
        
        <dt class="date"><?php echo Texg::get('profile-last_worth-title'); ?></dt>
        <dd class="date"><?php echo $user->date; ?></dd>
        
    </dl>
</div>

