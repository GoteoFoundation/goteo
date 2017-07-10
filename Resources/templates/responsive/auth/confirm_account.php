<?php

$errors = $this->errors;
$oauth = $this->oauth;
$user = $this->user;

$name = $oauth->user_data['name'];
$email = $oauth->user_data['email'];

if($this->userid) $username = $this->userid;
if($this->email) $email = $this->email;

if($this->provider_email) $provider_email = $this->provider_email;
else                      $provider_email = $email;

if(empty($name)) $name = strtok($email,"@");
if(is_numeric($username) || empty($username)) $username = \Goteo\Core\Model::idealiza(str_replace(" ","",$name));
if(empty($username)) $username = strtok($email,"@");
//Falta, si l'usuari existeix, suggerir un altre que estigui disponible...

$this->layout('auth/layout', [
    'title' => $this->text('reset-password-title'),
    'description' => $this->text('reset-password-title')
    ]);

$this->section('inner-content');
?>
    <h2 class="col-md-offset-1 padding-bottom-6"><?= $this->text('oauth-confirm-user') ?></h2>
    <div class="col-md-10 col-md-offset-1 reminder">
        <?= $this->text('oauth-goteo-openid-sync-password') ?>
    </div>
    <div class="col-md-10 col-md-offset-1 no-padding">
        <div class="col-md-4 no-padding">
            <img class="img-responsive" src="<?= $oauth->user_data['avatar'] ?>" alt="Profile image" >
        </div>
        <div class="col-md-8 standard-margin-top">
            <div><?= $user->name ?></div>
            <div><?= $email ?></div>
        </div>

    </div>

    <form class="form-horizontal" role="form" method="POST" action="/signup/oauth">

        <div class="form-group">
            <div class="col-md-10 col-md-offset-1 standard-margin-top">
                <input type="password"  class="form-control" value="<?= $username?>" placeholder="<?= $this->text('login-access-password-field') ?>" name="password">
            </div>
        </div>


        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">
                <button type="submit" class="btn btn-block btn-success"><?= $this->text('login-access-button') ?></button>
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
        echo '<input type="hidden" name="email" value="' . $email . '" />';
        echo '<input type="hidden" name="provider_email" value="' . $email . '" />';
        ?>
    </form>

<?php $this->replace() ?>
