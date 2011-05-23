<?php

$user = $this['user'];
$level = (int) $this['level'] ?: 3;

// @todo Esto ya debería venirme en $user
if (!isset($user->webs)) {
    $user->webs = \Goteo\Model\User\Web::get($user->id);
}
?>

<div class="widget user collapsable">
    
    <h<?php echo $level ?> class="title">Usuario</h<?php echo $level ?>>
    
    <h<?php echo $level + 1 ?> class="subtitle"><img />
    <?php echo htmlspecialchars($user->name) ?></h<?php echo $level + 1 ?>>
    
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
    
    <a class="button aqua profile" href="/user/<?php echo $user->id; ?>">Ver perfil</a>
        
</div>

