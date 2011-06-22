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
        
        <dt class="projects">Cofinancia</dt>
        <dd class="projects"><strong><?php echo $user->projects ?></strong> proyectos</dd>
        
        <dt class="worthcracy">Posición</dt>
        <dd class="worthcracy">            
            <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?> 
        </dd>
        
        <dt class="amount">Aporta</dt>
        <dd class="amount"><strong><?php echo number_format($user->amount) ?></strong> <span class="euro">&euro;</span></dd>
        
        <dt class="date">Fecha</dt>
        <dd class="date"><?php echo $user->date; ?></dd>
        
    </dl>
</div>

