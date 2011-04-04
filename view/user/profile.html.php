<?php $bodyClass = 'user-profile'; include 'view/prologue.html.php' ?>

    <?php include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2><?php if (1 || isset($user->avatar)): ?><img alt=""><?php endif ?> Perfil de <br /><em><?php echo htmlspecialchars($user->id) ?></em></h2>
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
        </div>
        
    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>