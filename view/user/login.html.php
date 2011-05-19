<?php

use Goteo\Core\View;

$bodyClass = 'user-login';
include 'view/prologue.html.php';
include 'view/header.html.php';

$error = $this['login_error'];
$errors = $this['errors'];
extract($_POST);
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
                        <label for="RegisterUsername">Nombre de usuario</label>
                        <input type="text" id="RegisterUsername" name="username" value="<?php echo htmlspecialchars($username) ?>"/></label>
                    </div>

                    <?php if(isset($errors['username'])) { ?><p><em><?php echo $errors['username']?></em></p><?php } ?>

                    <div class="email">
                        <label for="RegisterEmail">Email</label>
                        <input type="text" id="RegisterEmail" name="email" value="<?php echo htmlspecialchars($email) ?>"/></label>
                    </div>

                    <div class="remail">
                        <label for="RegisterREmail">Confirmar email</label>
                        <input type="text" id="RegisterREmail" name="remail" value="<?php echo htmlspecialchars($remail) ?>"/></label>
                    </div>

                    <?php if(isset($errors['email'])) { ?><p><em><?php echo $errors['email']?></em></p><?php } ?>

                    <div class="password">
                        <label for="RegisterPassword">Contraseña</label>
                        <input type="password" id="RegisterPassword" name="password" value="<?php echo htmlspecialchars($password) ?>"/></label>
                    </div>

                     <div class="rpassword">
                        <label for="RegisterRPassword">Confirmar contraseña</label>
                        <input type="password" id="RegisterRPassword" name="rpassword" value="<?php echo htmlspecialchars($rpassword) ?>"/></label>
                    </div>

                    <?php if(isset($errors['password'])) { ?><p><em><?php echo $errors['password']?></em></p><?php } ?>

                    <input type="submit" name="register" value="Registrar" />

            </form>
            </div>
        </div>

    </div>

<?php include 'view/footer.html.php' ?>