<?php 
$bodyClass = 'home';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

    <div id="main">

        <div id="login">

            <h2>Login</h2>

            <form action="" method="post">

                <fieldset>

                    <legend>Identificación de usuario</legend>

                    <ul>
                        <li><label for="LoginUsername">Usuario</label> <input type="text" id="LoginUser" name="username" value="<?php echo $username?>" /></li>
                        <li><label for="LoginPassword">Contraseña</label> <input type="password" id="LoginPassword" name="password" /></dd>
                        <?php if (isset($error)): ?>
                            <li><em>Login failed</em></li>
                        <?php endif ?>
                    </ul>
                    <input type="submit" value="Enviar" />
                </fieldset>
            </form>
        </div>

        <div id="registro">
            <h2>Registro</h2>
            <a href="/user/register">Regístrate gratis</a>
        </div>
        
    </div>

<?php include 'view/footer.html.php' ?>