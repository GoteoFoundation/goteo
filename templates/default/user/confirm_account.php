<?php

$errors = $this->errors;
$oauth = $this->oauth;

extract($oauth->user_data);

if($this->userid) $username = $this->userid;
if($this->email) $email = $this->email;

if($this->provider_email) $provider_email = $this->provider_email;
else                      $provider_email = $email;

if(empty($name)) $name = strtok($email,"@");
if(is_numeric($username) || empty($username)) $username = \Goteo\Core\Model::idealiza(str_replace(" ","",$name));
if(empty($username)) $username = strtok($email,"@");
//Falta, si l'usuari existeix, suggerir un altre que estigui disponible...

$this->layout('layout', [
    'bodyClass' => 'user-login',
    ]);

$this->section('content');

?>

     <div id="main">

        <div class="register">
            <div>
                <h2><?= $this->text('oauth-confirm-user') ?></h2>
                <p><?= $this->text('oauth-goteo-openid-sync-password') ?></p>
                <div>
                <?php
                    echo '<img style="padding-right:12px;float:left;" src="' . ($user->avatar ? $user->avatar->getLink(56, 56, true) : '') . '" alt="Profile image" />';
                    echo '<p style="padding-top:30px;">'.$user->name.''."<br />\n";
                    echo '<strong>'.$email.'</strong>'."</p>\n";
                ?>
                <div style="clear:both;"></div></div>
                <form action="/user/oauth_register" method="post">

                    <div class="password">
                        <label><?= $this->text('login-access-password-field') ?>
                        <input type="password" name="password" value="<?php echo $username?>" /></label>
                    </div>

                    <input type="submit" name="login" value="<?= $this->text('login-access-button') ?>" />

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
            </div>
        </div>

    </div>

<?php $this->append() ?>
