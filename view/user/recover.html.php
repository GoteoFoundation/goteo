<?php

use Goteo\Core\View;

$bodyClass = 'user-login';
include 'view/prologue.html.php';
include 'view/header.html.php';

$error = $this['error'];
extract($_POST);
?>
    <div id="main">

        <div class="login">

            <div>

                <h2>Recuperar contraseña</h2>

                <?php if (!empty($error)): ?>
                <p class="error">No se puede recuperar ninguna cuenta con estos datos</p>
                <?php endif ?>

                <form action="/user/recover" method="post">
                    <div class="username">
                        <label>Nombre de usuario
                        <input type="text" name="username" value="<?php echo $username?>" /></label>
                    </div>

                    <div class="email">
                        <label>Email de la cuenta
                        <input type="text" name="email" value="<?php echo $email?>" /></label>
                    </div>

                    <input type="submit" name="recover" value="Recuperar" />

                </form>

            </div>
        </div>

    </div>

<?php include 'view/footer.html.php' ?>