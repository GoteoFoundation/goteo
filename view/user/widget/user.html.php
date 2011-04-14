<?php

$user = $this['user'];
$level = (int) $this['level'] ?: 3;

// @todo Esto ya debería venirme en $user
if (!isset($user->webs)) {
    $user->webs = \Goteo\Model\User\Web::get($user->id);
}

?>

<div class="widget user collapsable">
    
    <h<?php echo $level ?>>Usuario</h<?php echo $level ?>>
    
    <h<?php echo $level + 1 ?>><img />
    <?php echo htmlspecialchars($user->id) ?></h<?php echo $level + 1 ?>>
    
    <?php if (isset($user->about)): ?>
    <blockquote class="about">
    <?php echo htmlspecialchars($user->about) ?>    
    </blockquote>
    <?php endif ?>
    
    <dl>
        
        <?php if (isset($user->location)): ?>
        <dt class="location">Ubicación</dt>
        <dd class="location"><a href="">Barcelona, ES</a></dd>
        <?php endif ?>
        
        <?php if (!empty($user->webs)): ?>        
        <dt class="links">Webs</dt>        
        <dd class="links">
            <ul>
                <?php foreach ($user->webs as $link): ?>
                <li><a href="<?php echo htmlspecialchars($link->url) ?>"><?php echo htmlspecialchars($link->url) ?></a></li>
                <?php endforeach ?>
            </ul>
        </dd>                
        <?php endif ?>
        
    </dl>
    
    <div class="actions">
        <a href="">Enviar mensaje</a>
        <a href="">Ver perfil</a>
    </div>
     
</div>

