<?php 

use Goteo\Core\View;

$bodyClass = 'user-login';
include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>
    <div id="main">

        <div class="login">
            
            <div>

                <h2>Usuario registrado</h2>

                <?php if (isset($error)): ?>
                <p class="error">Login failed</p>
                <?php endif ?>

                <form action="/user/login" method="post">

                    <div class="username">
                        <label>Nombre de usuario 
                        <input type="text" name="username" value="<?php echo $username?>" /></label>
                    </div>

                    <div class="password">
                        <label>Contraseña 
                        <input type="password" name="password" value="<?php echo $username?>" /></label>
                    </div>

                    <input type="submit" name="login" value="Entrar" />                

                </form>
                
            </div>
        </div>
        
        <div class="external-login">
            <div>
            <h2>Accede con un solo click</h2>
            </div>
        </div>
        
                <div class="register">
            <div>
                <h2>Nuevo usuario</h2>
                <form action="/user/register" method="post">                            
                    
                    <div class="username">
                        <label>Nombre de usuario
                        <input type="text" id="RegisterUsername" name="username" value="<?php echo htmlspecialchars($username) ?>"/></label>
                    </div>
                    
                    <div class="email">
                        <label>E-mail
                        <input type="text" name="email" value="<?php echo htmlspecialchars($email) ?>"/></label>
                    </div>
                                                            
                    <div class="remail">
                        <label>Confirmar e-mail
                        <input type="text" name="remail" value="<?php echo htmlspecialchars($remail) ?>"/></label>
                    </div>
                    
                    <div class="password">
                        <label>Contraseña
                        <input type="password" name="password" value="<?php echo htmlspecialchars($password) ?>"/></label>
                    </div>
                    
                     <div class="rpassword">
                        <label>Confirmar contraseña
                        <input type="password" name="rpassword" value="<?php echo htmlspecialchars($rpassword) ?>"/></label>
                    </div>
                    
                    <input type="submit" name="register" value="Registrar" />                                        
                
            </form>
            </div>
        </div>
        
    </div>

<?php include 'view/footer.html.php' ?>