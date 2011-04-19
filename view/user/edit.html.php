<?php
$bodyClass = 'user-edit';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$errors = $this['errors'];
?>

    <div id="main">
        <h2>Editar perfil</h2>
        <form action="" method="post">
            <div class="superform">
                <div class="elements">
                    <h3>Usuario/Acceso</h3>
                    <ol>
            			<li class="element textbox" id="user_id">
            				<label class="title" for="UserId">Nombre de usuario</label>
            				<div class="contents">
            					<strong><?php echo $user->id ?></strong>
            				</div>
            			</li>
            			<li class="element textbox" id="user_email">
            				<label class="title" for="UserEmail">E-mail</label>
            				<div class="contents">
            					<strong><?php echo $user->email ?></strong>
            				</div>
                            <br/>
            				<label class="title" for="UserNEmail">Nuevo E-mail</label>
            				<div class="contents">
            					<input type="text" name="user_nemail" id="UserNEmail" value="<?php echo $user->nemail ?>" size="20" />
            				</div>
                            <br/>
            				<label class="title" for="UserREmail">Confirmar nuevo E-mail</label>
            				<div class="contents">
            					<input type="text" name="user_remail" id="UserREmail" value="<?php echo $user->remail ?>" size="20" />
            				</div>
            			</li>
                        <?php if(isset($errors['email'])) { ?><li><em><?php echo $errors['email']?></em></li><?php } ?>
            			<li class="element textbox required" id="user_password">
            				<label class="title" for="UserPassword">Contraseña actual</label>
            				<div class="contents">
            					<input type="password" name="user_password" id="UserPassword" value="<?php echo $user->password ?>" size="20" />
            				</div>
                            <br/>
            				<label class="title" for="UserNPassword">Nueva Contraseña</label>
            				<div class="contents">
            					<input type="password" name="user_npassword" id="UserNPassword" value="<?php echo $user->npassword ?>" size="20" />
            				</div>
                            <br/>
            				<label class="title" for="UserRPassword">Confirmar nueva Contraseña</label>
            				<div class="contents">
            					<input type="password" name="user_rpassword" id="UserRPassword" value="<?php echo $user->rpassword ?>" size="20" />
            				</div>
            			</li>
                        <?php if(isset($errors['password'])) { ?><li><em><?php echo $errors['password']?></em></li><?php } ?>
                    </ol>
<?php include 'view/user/edit.profile.html.php' ?>
				</div>
                <div class="footer">
                    <div class="elements">
                        <div class="element">
                            <input type="submit" name="save" value="Guardar" class="save" />
                        </div>
                    </div>
                </div>
    		</div>
    	</form>
        <script type="text/javascript">

        jQuery(document).ready(function($) {

            var frm = $('.superform');
            frm.__chint= null;

            var els = frm.children('div.elements');

            var speed = 200;

            frm.find('li.element').each(function (i, li) {

                li = $(li);

                var id = li.attr('id');

                    if (frm.__chint !== id) {

                        if (frm.__chint !== null) {
                            setTimeout(function() {
                                frm.find('div.feedback#superform-feedback-for-' + frm.__chint + ':visible').fadeOut(speed);
                                frm.__chint = null;
                            }, 0);
                        }

                        setTimeout(function() {
                                frm.find('div.feedback#superform-feedback-for-' + id).not(':visible').fadeIn(speed);
                                frm.__chint = id;
                        }, 0);

                    }

            });

        });
        </script>
    </div>
<?php include 'view/footer.html.php' ?>