<?php
$user = $this['user'];
$errors = $this['errors'];
?>
            <fieldset>
                <legend>Perfil público</legend>
                <ol>
                    <li><label for="EditName">Nombre completo</label> <input type="text" id="EditName" name="name" value="<?php echo $user->name ?>"/></li>
                     <?php if(isset($errors['name'])) { ?><li><em><?php echo $errors['name']?></em></li><?php } ?>
                    <li><label for="EditAvatar">Tu imagen</label> <input type="file" id="EditAvatar" name="avatar" /></li>
                    <?php if(isset($errors['avatar'])) { ?><li><em><?php echo $errors['avatar']?></em></li><?php } ?>
                    <li>
                        <label for="EditAbout">Cuéntanos algo sobre tí</label>
                        <textarea id="EditAbout" name="about"><?php echo $user->about ?></textarea>
                    </li>
                    <?php if(isset($errors['about'])) { ?><li><em><?php echo $errors['about']?></em></li><?php } ?>
                    <li>
                        <label for="EditInterests">Intereses</label>
                        <textarea id="EditInterests" name="interests"><?php echo $user->interests ?></textarea>
                    </li>
                    <?php if(isset($errors['interests'])) { ?><li><em><?php echo $errors['interests']?></em></li><?php } ?>
                    <li>
                        <label for="EditContribution">¿Qué podrías aportar a Goteo?</label>
                        <textarea id="EditContribution" name="contribution"><?php echo $user->contribution ?></textarea>
                    </li>
                    <?php if(isset($errors['contribution'])) { ?><li><em><?php echo $errors['contribution']?></em></li><?php } ?>
                    <li>
                        <dl>
                            <dt><label for="EditBlog">Blog</label></dt>
                            <dd>http://<input type="text" id="EditBlog" name="blog" value="<?php echo $user->blog ?>" /></dd>
                            <?php if(isset($errors['web'])) { ?><li><em><?php echo $errors['web']?></em></li><?php } ?>
                            <dt><label for="EditTwitter">Twitter</label></dt>
                            <dd>http://twitter.com/<input type="text" id="EditTwitter" name="twitter" value="<?php echo $user->twitter ?>" /></dd>
                            <dt><label for="EditFacebook">Facebook</label></dt>
                            <dd>http://facebook.com/<input type="text" id="EditFacebook" name="facebook" value="<?php echo $user->facebook ?>" /></dd>
                            <dt><label for="EditLinkedin">Linkedin</label></dt>
                            <dd>http://linkedin.com/<input type="text" id="EditLinkedin" name="linkedin" value="<?php echo $user->linkedin ?>" /></dd>
                        </dl>
                    </li>
                </ol>
            </fieldset>
