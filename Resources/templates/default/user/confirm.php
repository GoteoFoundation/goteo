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
                <h2><?= $this->text('login-register-header') ?></h2>
                <p><?= $this->text('oauth-login-welcome-from') ?></p>
                <form action="/user/oauth_register" method="post">

                    <div class="userid">
                        <label for="RegisterUserid"><?= $this->text('login-register-userid-field') ?></label>
                        <input type="text" id="RegisterUserid" name="userid" value="<?= $username ?>"/>
                    <?php if(isset($errors['userid'])) { ?><em><?php echo $errors['userid']?></em><?php } ?>
                    </div>

                    <div class="email">
                        <label for="RegisterEmail"><?= $this->text('login-register-email-field') ?></label>
                        <input type="text" id="RegisterEmail" name="email" value="<?= $email ?>"/>
                    <?php if(isset($errors['email'])) { ?><em><?php echo $errors['email']?></em><?php } ?>
                    </div>

                    <input class="checkbox" id="register_accept" name="confirm" type="checkbox" value="true" />
                    <label class="conditions" for="register_accept"><?= $this->text_html('login-register-conditions') ?></label><br />

                    <button class="disabled" disabled="disabled" id="register_continue" name="register" type="submit" value="register"><?= $this->text('login-register-button') ?></button>

					<?php

					//tokens para poder saber que es un registro automatico
					foreach($oauth->tokens as $key => $val) {
						if($val['token']) echo '<input type="hidden" name="tokens[' . $key . '][token]" value="' . htmlspecialchars($val['token']) . '" />';
						if($val['secret']) echo '<input type="hidden" name="tokens[' . $key . '][secret]" value="' . htmlspecialchars($val['secret']) . '" />';
					}
					//data extra para incluir al usuario
					foreach($oauth->user_data as $key => $val) {
						if($val && $key!='email') echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($val) . '" />';
					}
					//proveedor
					echo '<input type="hidden" name="provider" value="' . $oauth->original_provider . '" />';
					//email original, para saber si se ha cambiado
					echo '<input type="hidden" name="provider_email" value="' . $provider_email . '" />';
					?>

				</form>
            </div>
        </div>

		<div style="width:500px;">
			<p><?= $this->text('oauth-login-imported-data') ?></p>
			<?php

			if($avatar) echo '<img style="float:left;width:200px;max-height:200px;" src="'.$avatar.'" alt="Imported profile image" />';
			echo "<div>";
			foreach(array_merge($oauth->import_user_data, array('website')) as $k) {
				if($$k && $k != 'avatar') echo '<strong>' . $this->text('oauth-import-'.$k) . ':</strong><br />'.nl2br($$k)."<br /><br />\n";
			}

			echo "</div>\n";
			?>
		</div>
    </div>



<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
jQuery(document).ready(function($) {
    $("#register_accept").click(function (event) {
        if (this.checked) {
            $("#register_continue").removeClass('disabled').addClass('aqua');
            $("#register_continue").removeAttr('disabled');
        } else {
            $("#register_continue").removeClass('aqua').addClass('disabled');
            $("#register_continue").attr('disabled', 'disabled');
        }
    });
});
// @license-end
</script>

<?php $this->append() ?>
