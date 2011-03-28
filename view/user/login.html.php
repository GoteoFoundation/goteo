<?php include 'view/header.html.php' ?>
    <div id="login">
        <h2>Login</h2>
        <form action="/user/login" method="post">
            <fieldset>
                <legend>Identificación de usuario</legend>
                <ol>
                    <li><label for="LoginUsername">Usuario</label> <input type="text" id="LoginUser" name="username" value="<?php echo $username?>" /></li>
                    <li><label for="LoginPassword">Contraseña</label> <input type="password" id="LoginPassword" name="password" /></dd>
<?php if(isset($error)) { ?>
                    <li><em>Login failed</em></li>
<?php } ?>
                </ol>
                <p><input type="submit" name="login" value="Enviar" /></p>
            </fieldset>
        </form>
    </div>
    <div id="registro">
        <h2>Registro</h2>
        <a href="/user/register">Regístrate gratis</a>
    </div>
<?php include 'view/footer.html.php' ?>