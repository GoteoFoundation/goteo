<?php
$bodyClass = 'user-edit';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>
    <div id="main">
        <h2>Editar</h2>
        <form action="" method="post">
            <fieldset>
                <legend>Datos de acceso</legend>
                <ol>
                    <li><label for="EditUsername">Nombre de usuario</label> <strong><?php echo $_SESSION['user']->id ?></strong></input></li>
                    <li><label for="EditUsername">Email</label> <strong><?php echo $_SESSION['user']->email ?></strong></input></li>
                    <li><label for="EditEmail">Nuevo email</label> <input type="text" id="EditEmail" name="email" value="<?php echo $email ?>"/></li>
                    <li><label for="EditREmail">Confirmar nuevo email</label> <input type="text" id="EditREmail" name="remail" value="<?php echo $remail ?>"/></li>
                    <?php if(isset($errors['email'])) { ?><li><em><?php echo $errors['email']?></em></li><?php } ?>
                    <li><label for="EditAPassword">Contraseña actual</label> <input type="password" id="EditAPassword" name="apassword" value="<?php echo $apassword ?>"/></li>
                    <li><label for="EditPassword">Contraseña nueva</label> <input type="password" id="EditPassword" name="password" value="<?php echo $password ?>"/></li>
                    <li><label for="EditRPassword">Confirmar contraseña nueva</label> <input type="password" id="EditRPassword" name="rpassword" value="<?php echo $rpassword ?>"/></li>
                    <?php if(isset($errors['password'])) { ?><li><em><?php echo $errors['password']?></em></li><?php } ?>
                </ol>
            </fieldset>
<?php include 'view/user/edit.profile.html.php' ?>
        </form>
    </div>
<?php include 'view/footer.html.php' ?>