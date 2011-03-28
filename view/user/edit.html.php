<?php include 'view/header.html.php' ?>
    <div id="editar">
        <h2>Editar</h2>
        <style type="text/css">
            /** @FIXME: BORRAME **/
            li textarea { display: block; }
        </style>
        <form action="/user/edit" method="post">
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
            <fieldset>
                <legend>Perfil público</legend>
                <ol>
                    <li><label for="EditName">Nombre completo</label> <input type="text" id="EditName" name="name" value="<?php echo $user->name ?>"/></li>
                    <li><label for="EditAvatar">Tu imagen</label> <input type="file" id="EditAvatar" name="avatar" /></li>
                    <li>
                        <label for="EditAbout">Cuéntanos algo sobre tí</label>
                        <textarea id="EditAbout" name="about"><?php echo $user->about ?></textarea>
                    </li>
                    <li>
                        <label for="EditInterests">Intereses</label>
                        <textarea id="EditInterests" name="interests"><?php echo $user->interests ?></textarea>
                    </li>
                    <li>
                        <label for="EditContribution">¿Qué podrías aportar a Goteo?</label>
                        <textarea id="EditContribution" name="contribution"><?php echo $user->contribution ?></textarea>
                    </li>
                    <li>
                        <dl>
                            <dt><label for="EditBlog">Blog</label></dt>
                            <dd>http://<input type="text" id="EditBlog" name="blog" value="<?php echo $user->blog ?>" /></dd>
                            <dt><label for="EditTwitter">Twitter</label></dt>
                            <dd>http://twitter.com/<input type="text" id="EditTwitter" name="twitter" value="<?php echo $user->twitter ?>" /></dd>
                            <dt><label for="EditFacebook">Facebook</label></dt>
                            <dd>http://facebook.com/<input type="text" id="EditFacebook" name="facebook" value="<?php echo $user->facebook ?>" /></dd>
                            <dt><label for="EditLinkedin">Linkedin</label></dt>
                            <dd>http://linkedin.com/<input type="text" id="EditLinkedin" name="linkedin" value="<?php echo $user->linkedin ?>" /></dd>
                        </dl>
                    </li>
                </ol>
                <p><input type="submit" name="register" value="Guardar" /></p>
            </fieldset>
        </form>
    </div>
<?php include 'view/footer.html.php' ?>