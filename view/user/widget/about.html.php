<?php

use Goteo\Model\User\Interest;

$user = $this['user'];

$interests = Interest::getAll();
?>

<div class="widget user-about">
    
    
    <?php if (!empty($user->about)): ?>    
    <div class="about">        
        <h4>Sobre mi</h4>
        <p><?php echo $user->about ?></p>
    </div>    
    <?php endif ?>
        
    <?php if (!empty($user->interests)): ?>    
    <div class="interests">        
        <h4>Mis intereses</h4>
        <p><?php
        $c = 0;
        foreach ($user->interests as $interest) {
            if ($c > 0) echo ', ';
            echo $interests[$interest];
            $c++;
        } ?></p>                
    </div>    
    <?php endif ?>
    
    <?php if (!empty($user->keywords)): ?>    
    <div class="keywords">        
        <h4>Mis etiquetas</h4>
        <p><?php echo $user->keywords; ?></p>        
    </div>
    <?php endif ?>
        
    <?php if (!empty($user->webs)): ?>
    <div class="webs">     
        <h4>Mis webs</h4>
        <ul>
            <?php foreach ($user->webs as $link): ?>
            <li><a href="<?php echo htmlspecialchars($link->url) ?>" target="_blank"><?php echo htmlspecialchars($link->url) ?></a></li>
            <?php endforeach ?>
        </ul>
    </div>
    <?php endif ?>
    
    <?php if (!empty($user->location)): ?>
     <div class="location">    
        <h4>Ubicación</h4>
        <!-- @todo pasar idioma a google maps -->
        <p><a href="http://maps.google.es/maps?q=<?php echo htmlspecialchars(rawurlencode($user->location)) ?>&hl=es" target="_blank"><?php echo htmlspecialchars($user->location) ?></a></p>
     </div>
    <?php endif ?>

</div>
