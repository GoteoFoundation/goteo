<?php

use Goteo\Core\View;

$bodyClass = 'user-profile';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$worthcracy = $this['worthcracy'];
?>

        <div id="sub-header">
            <div>
                <h2><?php if (1 || isset($user->avatar)): ?><img alt=""><?php endif ?> Perfil de <br /><em><?php echo $user->name; ?></em></h2>
            </div>
        </div>
        
        <div id="main">                                    
                                    
            <?php if (isset($user->about)): ?>
            <div class="about">
                <?php echo $user->about ?>
            </div>
            <?php endif ?>
            
            <?php if (isset($user->facebook) || isset($user->linkedin) || isset($user->twitter)): ?>            
            <div class="social">
                <ul>
                    <?php if (isset($user->facebook)): ?>
                    <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>">Facebook</a></li>
                    <?php endif ?>
                    <?php if (isset($user->twitter)): ?>
                    <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>">Twitter</a></li>
                    <?php endif ?>
                    <?php if (isset($user->linkedin)): ?>
                    <li class="linkedin"><a href="<?php echo htmlspecialchars($user->linkedin) ?>">LinkedIn</a></li>
                    <?php endif ?>
                </ul>                
            </div>            
            <?php endif ?>

            <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'type' => 'main', 'level' => $user->worth)); ?>

        </div>
        
    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>