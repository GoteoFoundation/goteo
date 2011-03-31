<?php $bodyClass = 'user-profile'; include 'view/prologue.html.php' ?>

    <?php include 'view/header.html.php' ?>
        
        <div id="main">
                    
            <h2>Perfil del usuario <strong><?php echo htmlspecialchars($user->id) ?></strong></h2>
            <dl>
                
                <dt><label for="name">Nombre completo</label></dt>
                <dd><?php echo $user->name ?></dd>
                
                <dt><label for="about">Detalles</label></dt>
                <dd><?php echo $user->about ?></dd>   
                
                <dt><label for="interests">Intereses</label></dt>
                <dd><?php echo $user->interests ?></dd>    
                
                <dt><label for="contribution">Manera en que contribuye</label></dt>
                <dd><?php echo $user->contribution ?></dd>    
                
                <dt><label for="blog">Blog</label></dt>
                <dd>http://<?php echo $user->blog ?></dd>    
                
                <dt><label for="twitter">Twitter</label></dt>
                <dd>http://twitter.com/<?php echo $user->twitter ?></dd>
                
                <dt><label for="facebook">Facebook</label></dt>
                <dd>http://facebook.com/<?php echo $user->facebook ?><dd>
                
                <dt><label for="linkedin">Linkedin</label></dt>
                <dd>http://linkedin.com/<?php echo $user->linkedin ?></dd>
                
            </dl>                    
        </div>
        
    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>