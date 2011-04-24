<?php
$bodyClass = 'user-register';
include 'view/prologue.html.php';
include 'view/header.html.php';

$errors = $this['errors'];
extract($_POST);
?>

    <div id="main">
        <form action="" method="post">
            <fieldset>
                <legend>Registro de usuario</legend>
                <ol>
                    <li><label for="RegisterUsername">Nombre de usuario *</label> <input type="text" id="RegisterUsername" name="username" value="<?php echo $username ?>"/></li>
                    <?php if(isset($errors['username'])) { ?><li><em><?php echo $errors['username']?></em></li><?php } ?>
                    <li><label for="RegisterEmail">Email *</label> <input type="text" id="RegisterEmail" name="email" value="<?php echo $email ?>"/></li>
                    <li><label for="RegisterREmail">Confirmar email *</label> <input type="text" id="RegisterREmail" name="remail" value="<?php echo $remail ?>"/></li>
                    <?php if(isset($errors['email'])) { ?><li><em><?php echo $errors['email']?></em></li><?php } ?>
                    <li><label for="RegisterPassword">Contraseña *</label> <input type="password" id="RegisterPassword" name="password" value="<?php echo $password ?>"/></li>
                    <li><label for="RegisterRPassword">Confirmar contraseña *</label> <input type="password" id="RegisterRPassword" name="rpassword" value="<?php echo $rpassword ?>"/></li>
                    <?php if(isset($errors['password'])) { ?><li><em><?php echo $errors['password']?></em></li><?php } ?>
                </ol>
                <p><input type="submit" name="register" value="Registrar" /></p>
            </fieldset>
        </form>
    </div>

<?php include 'view/footer.html.php' ?>