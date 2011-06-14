<?php
$user = $this['user'];
$errors = $this['errors'];
extract($_POST);
?>
<form action="/dashboard/profile/access" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
    <div class="superform">
        <div class="elements">
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
                </li>
                <li class="element inline textbox" id="user_nemail">
                    <br/>
                    <label class="title" for="UserNEmail">Nuevo E-mail</label>
                    <div class="contents">
                        <input type="text" name="user_nemail" id="UserNEmail" value="<?php echo $user_nemail ?>" size="20" />
                    </div>
<?php if(!is_array($errors['email']) && isset($errors['email'])) { ?>
                    <div class="feedback" id="superform-feedback-for-user_email">
                        <div class="hint">
                            <blockquote><?php echo $errors['email']?></blockquote>
                        </div>
                    </div>
<?php } ?>
                </li>
                <li class="element inline textbox" id="user_remail">
                    <label class="title" for="UserREmail">Confirmar nuevo E-mail</label>
                    <div class="contents">
                        <input type="text" name="user_remail" id="UserREmail" value="<?php echo $user_remail ?>" size="20" />
                    </div>
<?php if(is_array($errors['email']) && isset($errors['email']['retry'])) { ?>
                    <div class="feedback" id="superform-feedback-for-user_email">
                        <div class="hint">
                            <blockquote><?php echo $errors['email']['retry']?></blockquote>
                        </div>
                    </div>
<?php } ?>
                </li>
                <li class="element inline">
                    <div class="element">
                        <input type="submit" name="change_email" value="Cambiar E-mail" class="save" />
                    </div>
                </li>
                <a name="password"></a>
                <li class="element textbox" id="user_password">
                    <label class="title" for="UserPassword">Contraseña actual</label>
                    <div class="contents">
                        <input type="password" name="user_password" id="UserPassword" value="<?php echo $user_password ?>" size="20" />
                    </div>
                <?php if($this['action'] == 'recover') echo $this['message']; ?>
<?php if(!is_array($errors['password']) && isset($errors['password'])) { ?>
                    <div class="feedback" id="superform-feedback-for-user_password">
                        <div class="hint">
                            <blockquote><?php echo $errors['password']?></blockquote>
                        </div>
                    </div>
<?php } ?>
                </li>
                <li class="element inline textbox" id="user_npassword">
                    <label class="title" for="UserNPassword">Nueva Contraseña</label>
                    <div class="contents">
                        <input type="password" name="user_npassword" id="UserNPassword" value="<?php echo $user_npassword ?>" size="20" />
                    </div>
<?php if(is_array($errors['password']) && isset($errors['password']['new'])) { ?>
                    <div class="feedback" id="superform-feedback-for-user_npassword">
                        <div class="hint">
                            <blockquote><?php echo $errors['password']['new']?></blockquote>
                        </div>
                    </div>
<?php } ?>
                </li>
                <li class="element inline textbox" id="user_rpassword">
                    <label class="title" for="UserRPassword">Confirmar nueva Contraseña</label>
                    <div class="contents">
                        <input type="password" name="user_rpassword" id="UserRPassword" value="<?php echo $user_rpassword ?>" size="20" />
                    </div>
<?php if(is_array($errors['password']) && isset($errors['password']['retry'])) { ?>
                    <div class="feedback" id="superform-feedback-for-user_rpassword">
                        <div class="hint">
                            <blockquote><?php echo $errors['password']['retry']?></blockquote>
                        </div>
                    </div>
<?php } ?>
                </li>
                <li class="element inline">
                    <div class="element">
                        <input type="submit" name="change_password" value="Cambiar contraseña" class="save" />
                    </div>
                </li>
            </ol>
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

