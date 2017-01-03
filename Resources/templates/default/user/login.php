<?php

use Goteo\Core\View;

// para que el prologue ponga el código js para botón facebook en el bannerside
$fbCode = $this->text_widget($this->text('social-account-facebook'), 'fb');
$errors = $this->errors;

//Obtain oauth from remembering cookie
$goteo_oauth_provider = $this->get_cookie('goteo_oauth_provider');


$this->layout("layout", [
    'bodyClass' => 'user-login',
    'jscrypt' => 'true',
    ]);

$this->section('content');
?>
<div id="sub-header">
	<div class="clearfix">
		<div class="subhead-banner">
			<h2 class="message"><?= $this->text_html('login-banner-header') ?></h2>
		</div>
		<div class="mod-pojctopen"><?= $this->text_html('open-banner-header',$fbCode); ?></div>
	</div>
</div>

    <div id="main">
        <div class="login">
            <div>
                <h2><?= $this->text('login-access-header') ?></h2>

                <form action="<?php echo SEC_URL ?>/user/login" method="post" id="login_frm">
                    <input type="hidden" name="return" value="<?php echo $_GET['return']; ?>" />
                    <div class="username">
                        <label><?= $this->text('login-access-username-field') ?>
                        <input type="text" name="username" value="<?= $this->username ? $this->username : $this->get_cookie('goteo_user') ?>" /></label>
                    </div>

                    <div class="password">
                        <label><?= $this->text('login-access-password-field') ?>
                        <input type="password" id="thepw" name="password" value="" /></label>
                    </div>

                    <input type="submit" name="login" value="<?= $this->text('login-access-button') ?>" />

                </form>

                <p><a href="<?php echo SEC_URL; ?>/user/recover?email=<?= $this->username ?>"><?= $this->text('login-recover-link') ?></a></p>
                <br />
                <p><a class="baja" href="<?php echo SEC_URL; ?>/user/leave"><?= $this->text('login-leave-button') ?></a></p>

            </div>
        </div>

        <div class="external-login">
            <div>
                <h2><?= $this->text('login-oneclick-header') ?></h2>
                <ul class="sign-in-with">
                <?php

				//posarem primer l'ultim servei utilitzat
				//de manera que si l'ultima vegada t'has autentificat correctament amb google, el tindras el primer de la llista

				//l'ordre que es vulgui...
                $logins = array(
					'facebook' => '<a href="/user/oauth?provider=facebook">' . $this->text('login-signin-facebook') . '</a>',
					'twitter' => '<a href="/user/oauth?provider=twitter">' . $this->text('login-signin-twitter') . '</a>',
					'google' => '<a href="/user/oauth?provider=google">' . $this->text('login-signin-google') . '</a>',
					'Yahoo' => '<a href="/user/oauth?provider=Yahoo">' . $this->text('login-signin-yahoo') . '</a>',
					'Ubuntu' => '<a href="/user/oauth?provider=Ubuntu">' . $this->text('login-signin-ubuntu') . '</a>',
					'linkedin' => '<a href="/user/oauth?provider=linkedin">' . $this->text('login-signin-linkedin') . '</a>',
					'openid' => ''
                );
                $is_openid = !array_key_exists($goteo_oauth_provider, $logins);
                $logins['openid'] = '<form><input type="text"'.($is_openid ? ' class="used"' : '').' name="openid" value="' . htmlspecialchars( $is_openid ? $goteo_oauth_provider : $this->text('login-signin-openid')) . '" /><a href="/user/oauth" class="button">' . $this->text('login-signin-openid-go') . '&rarr;</a></form>';
                //si se ha guardado la preferencia, lo ponemos primero
                $key = '';
                if($goteo_oauth_provider) {
					$key = array_key_exists($goteo_oauth_provider,$logins) ? $goteo_oauth_provider : 'openid';
					echo '<li class="'.strtolower($key).'">'.$logins[$key].'</li>';
					echo '<li class="more">&rarr;<a href="#">'.$this->text('login-signin-view-more').'</a></li>';

				}
                foreach($logins as $k => $v) {
					if($key != $k) echo '<li class="'.strtolower($k) .'"'. ( $goteo_oauth_provider ? ' style="display:none"' :'') .'>'.$v.'</li>';
				}
                ?>

                </ul>
            </div>
        </div>

        <div class="register">
            <div>
                <h2><?= $this->text('login-register-header') ?></h2>
                <form action="<?php echo SEC_URL; ?>/user/register" method="post">

                    <div class="userid">
                        <label for="RegisterUserid"><?= $this->text('login-register-userid-field') ?></label>
                        <input type="text" id="RegisterUserid" name="userid" value="<?= $this->userid ?>" maxlength="15" />
                    <?php if(isset($errors['userid'])) { ?><em><?php echo $errors['userid']?></em><?php } ?>
                    </div>

                    <div class="username">
                        <label for="RegisterUsername"><?= $this->text('login-register-username-field') ?></label>
                        <input type="text" id="RegisterUsername" name="username" value="<?= $this->username ?>" maxlength="20" />
                    <?php if(isset($errors['username'])) { ?><em><?php echo $errors['username']?></em><?php } ?>
                    </div>

                    <div class="email">
                        <label for="RegisterEmail"><?= $this->text('login-register-email-field') ?></label>
                        <input type="text" id="RegisterEmail" name="email" value="<?= $this->email ?>"/>
                    <?php if(isset($errors['email'])) { ?><em><?php echo $errors['email']?></em><?php } ?>
                    </div>

                    <div class="remail">
                        <label for="RegisterREmail"><?= $this->text('login-register-confirm-field') ?></label>
                        <input type="text" id="RegisterREmail" name="remail" value="<?= $this->remail ?>"/>
                    <?php if(isset($errors['remail'])) { ?><em><?php echo $errors['remail']?></em><?php } ?>
                    </div>


                    <div class="password">
                        <label for="RegisterPassword"><?= $this->text('login-register-password-field') ?></label> <?php if (strlen($this->password) < 6) echo '<em>' . $this->text('login-register-password-minlength') . '</em>'; ?>
                        <input type="password" id="RegisterPassword" name="password" value="<?= $this->password ?>"/>
                    <?php if(isset($errors['password'])) { ?><em><?php echo $errors['password']?></em><?php } ?>
                    </div>

                     <div class="rpassword">
                        <label for="RegisterRPassword"><?= $this->text('login-register-confirm_password-field') ?></label>
                        <input type="password" id="RegisterRPassword" name="rpassword" value="<?= $this->rpassword ?>"/>
                    <?php if(isset($errors['rpassword'])) { ?><em><?php echo $errors['rpassword']?></em><?php } ?>
                    </div>


                    <input class="checkbox" id="register_accept" name="confirm" type="checkbox" value="true" />
                    <label class="conditions" for="register_accept"><?= $this->text_html('login-register-conditions') ?></label><br />

                    <button class="disabled" disabled="disabled" id="register_continue" name="register" type="submit" value="register"><?= $this->text('login-register-button') ?></button>

            </form>
            </div>
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

    //openid
    $('.sign-in-with li.openid input').focus(function(){
        $(this).addClass('focus');
        if($(this).val() == '<?= $this->text('login-signin-openid') ?>') $(this).val('');
    });
    $('.sign-in-with li.openid input').blur(function(){
        $(this).removeClass('focus');
        if($(this).val().trim() == '') $(this).val('<?= $this->text('login-signin-openid') ?>');
    });
    $('.sign-in-with li.openid a').click(function(){
        $(this).attr('href',$(this).attr('href') + '?provider=' + $('.sign-in-with li.openid input').val());
        return true;
    });
    $('.sign-in-with li.openid input').keypress(function(event) {
        if ( event.which == 13 ) {
            event.preventDefault();
            location = $('.sign-in-with li.openid a').attr('href') + '?provider=' + $(this).val();
        }
    });

    //view more
    $('.sign-in-with li.more a').click(function(){
        $(this).parent().remove();
        $('.sign-in-with li:hidden').slideDown();
        return false;
    });

    $("#login_frm").submit(function () {
        $("#thepw").val(hex_sha1($("#thepw").val()));
        return true;
    });



});
// @license-end
</script>

<?php $this->append() ?>
