<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$bodyClass = 'user-profile';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$worthcracy = Worth::getAll();
?>
<div id="sub-header">
    <div>
        <h2><a href="/user/<?php echo $user->id; ?>"><img src="/image/<?php echo $user->avatar->id; ?>/75/75/1" /></a> <?php echo Text::get('profile-name-header'); ?> <br /><em><?php echo $user->name; ?></em></h2>
    </div>
</div>

<div id="main">

    <div class="center">
       
       
       <!-- lista de categorías -->
        <div class="widget categorylist">
            <h3 class="title"><?php echo Text::get('profile-sharing_interests-header');?></h3>
            <div class="filters">
                <span>Ver por:</span>
                <ul>
                    <li><a href="#" class="active">Por categorías</a></li>
                    <li class="separator">|</li>
                    <li><a href="#">Por tags</a></li>                
                </ul>
            </div>
            <div class="list">
                <ul>
                    <li><a href="#">Sociales</a></li>
                    <li><a href="#">Comunicadores</a></li>                
                    <li><a href="#" class="active">Tecnológicos</a></li>                
                    <li><a href="#">Emprendedores</a></li>                
                    <li><a href="#">Didácticos</a></li>            
                    <li><a href="#">Creativos</a></li>                            
                    <li><a href="#">Sociales</a></li>
                    <li><a href="#">Comunicadores</a></li>                                
                </ul>
            </div>
        </div>
        <!-- fin lista de categorías -->
        
        <!-- detalle de categoría (cabecera de categoría) -->
        <div class="widget user-mates">
            <h3 class="title">Tecnológicos</h3>
            <div class="users">
                <ul>
                <?php 
				$cnt = 1;
				foreach ($this['shares'] as $mate){ ?>
                    <li class="activable<?php if($cnt==1 or $cnt==2) echo " bordertop"?>">
                        <div class="user">
                            <div class="avatar"><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><img src="/image/<?php echo $mate->avatar->id ?>/43/43/1" /></a></div>
                            <h4><a href="/user/<?php echo htmlspecialchars($mate->user) ?>"><?php echo htmlspecialchars($mate->user) ?></a></h4>
                            <span class="projects"><?php echo Text::get('regular-projects'); ?> (<?php echo $mate->projects ?>)</span>
                            <span class="invests"><?php echo Text::get('regular-investing'); ?> (<?php echo $mate->invests ?>)</span><br/>
                            <span class="profile"><a href="/user/<?php echo htmlspecialchars($mate->user) ?>">Ver perfil</a> </span>
                            <span class="contact"><a href="/user/profile/<?php echo htmlspecialchars($mate->user) ?>/message">Escribir mensaje</a></span>
                        </div>
                    </li>
                <?php 
				$cnt ++;
				} ?>
                </ul>
            </div>
        </div>
        <!-- fin detalle de categoría (cabecera de categoría) -->
        
    </div>
    <div class="side">
        <?php echo new View('view/user/widget/investors.html.php', $this) ?>
        <?php echo new View('view/user/widget/user.html.php', $this) ?>
    </div>

</div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>
