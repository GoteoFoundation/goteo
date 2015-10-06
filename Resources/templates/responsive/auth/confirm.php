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
<div class="container">

	<div class="row row-form">
			<div class="panel panel-default panel-form">
				<div class="panel-body">
					<h2 class="col-md-offset-1 padding-bottom-6"><?= $this->text('login-register-header') ?></h2>
                	<p><?= $this->text('oauth-login-welcome-from') ?></p>
					<div>  
					<form class="form-horizontal" role="form" method="POST" action="oauthAction">

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="text" class="form-control" placeholder="<?= $this->text('login-register-userid-field') ?>" name="userid">
							</div>
							<?php if(isset($errors['userid'])) { ?><em><?php echo $errors['userid']?></em><?php } ?>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="text" class="form-control" placeholder="<?= $this->text('login-register-email-field') ?>" name="RegisterEmail">
							
                    <?php if(isset($errors['email'])) { ?><em><?php echo $errors['email']?></em><?php } ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<div class="checkbox">
									<label>
										<input type="checkbox" >
											<p>
											<?= $this->text_html('login-register-conditions') ?> <a data-toggle="modal" data-target="#myModal" href="">Más información.</a>.
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
	                    echo '<input type="hidden" name="email" value="' . $email . '" />';
	                    echo '<input type="hidden" name="provider_email" value="' . $email . '" />';
	                    ?>
					</form>		

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
			</div>
		
	</div>
</div>

<?php $this->replace() ?>