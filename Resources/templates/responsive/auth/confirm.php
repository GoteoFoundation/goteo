<?php

$errors = $this->errors;
$oauth = $this->oauth;

$name = $oauth->user_data['name'];
$email = $oauth->user_data['email'];

if($this->userid) $username = $this->userid;
if($this->email) $email = $this->email;

if($this->provider_email) $provider_email = $this->provider_email;
else                      $provider_email = $email;


if(empty($name)) $name = strtok($email,"@");
if(is_numeric($username) || empty($username)) $username = $this->sanitize(str_replace(" ","",$name));
if(empty($username)) $username = strtok($email,"@");
//Falta, si l'usuari existeix, suggerir un altre que estigui disponible...

$this->layout('auth/layout');

$this->section('inner-content');
?>
    <h2 class="col-md-offset-1 padding-bottom-6"><?= $this->text('login-register-header') ?></h2>
    <div class="col-md-10 col-md-offset-1 reminder">
        <?= $this->text('oauth-login-welcome-from') ?>
    </div>
    <div>
    <form class="form-horizontal" role="form" method="POST" action="/signup/oauth">

        <div class="form-group<?= (isset($errors['userid']) ? ' has-error' : ($username ? ' has-success' : '')) ?>">
            <div class="col-md-10 col-md-offset-1">
                <input type="text" required class="form-control" placeholder="<?= $this->text('login-register-userid-field') ?>" name="userid" value="<?= $username ?>">
                <?php if(isset($errors['userid'])) { ?><span class="help-block"><?php echo $errors['userid']?></span><?php } ?>
            </div>
        </div>

        <div class="form-group<?= (isset($errors['email']) ? ' has-error' : ($email ? ' has-success' : '')) ?>">
            <div class="col-md-10 col-md-offset-1">
                <input type="email" required class="form-control" placeholder="<?= $this->text('login-register-email-field') ?>" name="email" value="<?= $email ?>">
                <?php if(isset($errors['email'])) { ?><span class="help-block"><?php echo $errors['email']?></span><?php } ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" class="no-margin-checkbox">
                            <p class="label-checkbox">
                            <?= $this->text('login-register-conditions') ?>.
                            </p>
                    </label>
                </div>
            </div>
        </div>


        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">
                <button type="submit" class="btn btn-block btn-success" value=""><?= $this->text('login-register-button') ?></button>
            </div>
        </div>



        <?php

        //tokens para poder saber que es un registro automatico
        foreach($oauth->tokens as $key => $val) {
            if($val['token']) echo '<input type="hidden" name="tokens[' . $key . '][token]" value="' . $val['token'] . '" />';
            if($val['secret']) echo '<input type="hidden" name="tokens[' . $key . '][secret]" value="' . $val['secret'] . '" />';
        }

        //data extra para incluir al usuario
        foreach($oauth->user_data as $key => $val) {
            if($val && $key!='email') echo '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
        }
        //proveedor
        echo '<input type="hidden" name="provider" value="' . $oauth->original_provider . '" />';
        //email original
        echo '<input type="hidden" name="provider_email" value="' . $email . '" />';
        ?>
    </form>

    <div class="col-md-10 col-md-offset-1 no-padding">
        <p><?= $this->text('oauth-login-imported-data') ?></p>

        <?php
        if($oauth->user_data['avatar']):
        ?>
            <div class="col-md-5 no-padding">
                <img class="img-responsive" src="<?= $oauth->user_data['avatar'] ?>" alt="Imported profile image" />
            </div>
            <div class="col-md-7">
            <?php
                foreach(array_merge($oauth->import_user_data, array('website')) as $k) {
                    if($oauth->user_data[$k] && $k != 'avatar') echo '<strong>' . $this->text('oauth-import-'.$k) . ':</strong><br />'.nl2br($oauth->user_data[$k])."<br />\n";
                }
            ?>
            </div>
        <?php endif ?>
    </div>

<?php $this->replace() ?>
