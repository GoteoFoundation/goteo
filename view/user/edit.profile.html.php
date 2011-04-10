<?php
$bodyClass = 'user-edit';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
?>
    <div id="main">
        <h2>Editar perfil</h2>
        <form action="" method="post">
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