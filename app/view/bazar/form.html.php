<?php
	use Goteo\Library\Text,
	    Goteo\Model\Invest,
	    Goteo\Model\Call;

    $data = $_SESSION['bazar-form-data'];

	$item = $vars['item'];
	$page = $vars['page'];

	$debug = $page->debug;
    $allowpp = $item->project->allowpp;
?>
<script type="text/javascript">
var mandatory = '<?php echo Text::get('regular-mandatory') ?>';
var noemail = '<?php echo Text::get('error-contact-email-invalid') ?>';
var noremail = '<?php echo Text::get('error-register-email-confirm') ?>';

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};
function vfield(key){
	if ($('#'+key).val() == '') {
		$('#'+key+'i').html(mandatory);
	} else {
		$('#'+key+'i').html('');
		return true;
	}
	return false;
}
function valemail(){
	var key = '#email';
	var value = $(key).val();
	if (value == '') {
		$(key+'i').html(mandatory);
	} else if (!isValidEmailAddress(value)) {
		$(key+'i').html(noemail);
	} else {
		$(key+'i').html('');
		return true;
	}
	return false;
}
function valemail(){
	var evalue = $('#email').val();
	var key = '#remail';
	var value = $(key).val();
	if (value == '') {
		$(key+'i').html(mandatory);
	} else if (!isValidEmailAddress(value)) {
		$(key+'i').html(noemail);
	} else if (value != evalue) {
		$(key+'i').html(noremail);
	} else {
		$(key+'i').html('');
		return true;
	}
	return false;
}
function valnomdest(){
	var key = '#namedest';
	var value = $(key).val();
	var check = document.getElementById("check");
    if (check.checked) {
    	if (value == '') {
    		$(key+'i').html(mandatory);
    	} else {
    		$(key+'i').html('');
			return true;
    	}
	} else {
		$(key+'i').html('');
		return true;
	}
	return false;
}
function valemdest(){
	var key = '#emaildest';
	var value = $(key).val();
	var check = document.getElementById("check");
    if (check.checked) {
    	if (value == '') {
    		$(key+'i').html(mandatory);
    	} else if (!isValidEmailAddress(value)) {
    		$(key+'i').html(noemail);
    	} else {
    		$(key+'i').html('');
			return true;
    	}
	} else {
		$(key+'i').html('');
		return true;
	}
	return false;
}
function showContent() {
    var check = document.getElementById("check");
    if (check.checked) {
    	$(".friend").show();
    }
    else {
		$("#namedesti").html('');
		$("#emaildesti").html('');
    	$(".friend").hide();
    }
}
function validar(){
	return (vfield('name') && valemail() && valremail() && vfield('nomdest') && valemdest() && vfield('address') && vfield('location') && vfield('cp') && vfield('country'));
}
function valida(){
	return (vfield('user') && vfield('nomdest') && valemdest() && vfield('address') && vfield('location') && vfield('cp') && vfield('country'));
}
</script>

<article id="formulario">

	<form method="post" action="/bazaar/pay/<?php echo $item->id; ?>" onsubmit="<?php echo (isset($_SESSION['user'])) ? 'return valida();' : 'return validar();' ?>" >

		<div id="sendto" class="formfields alone">
			<label><input type="checkbox" onchange="showContent();" id="check" name="regalo" value="1" <?php if ($data['regalo']) echo 'checked="checked"'; ?>/><?php echo Text::get('invest-address-friend-field') ?></label>
		</div>

		<?php if (isset($_SESSION['user'])) : ?>
			<input type="hidden" name="user" value="<?php echo $_SESSION['user']->id; ?>" />
			<input type="hidden" name="name" value="<?php echo $_SESSION['user']->name; ?>" />
			<input type="hidden" name="email" value="<?php echo $_SESSION['user']->email; ?>" />
			<p><?php echo Text::get('regular-hello').' '.$_SESSION['user']->name; ?><span class="error" id="useri"></span></p>
		<?php else : ?>
		<div id="fields-investor" class="formfields">
			<div class="field">
				<label for="name"><?php echo Text::get('invest-address-myname-field') ?> *</label><span class="error" id="namei"></span><br />
				<input type="text" onblur="vfield('name');" id="name" name="name" value="<?php echo $data['name']; ?>" />
			</div>

			<div class="field">
				<label for="email"><?php echo Text::get('invest-address-myemail-field') ?> *</label><span class="error" id="emaili"></span><br />
				<input type="text" onblur="valemail();" id="email" name="email" value="<?php echo $data['email']; ?>" />
			</div>

			<div class="field">
				<label for="remail"><?php echo Text::get('login-register-confirm-field') ?> *</label><span class="error" id="remaili"></span><br />
				<input type="text" onblur="valremail();" id="remail" name="remail" value="<?php echo $data['remail']; ?>" />
			</div>
		</div>
		<?php endif; ?>

		<div id="fields-friend" class="formfields friend"<?php if ($data['regalo']) echo ' style="display:block;"'; ?>>
			<div class="field">
				<label for="namedest"><?php echo Text::get('invest-address-namedest-field') ?> *</label><span class="error" id="namedesti"></span><br />
				<input type="text" onblur="valnomdest();" id="namedest" name="namedest" value="<?php echo $data['namedest']; ?>" />
			</div>

			<div class="field">
				<label for="emaildest"><?php echo Text::get('invest-address-maildest-field') ?> *</label><span class="error" id="emaildesti"></span><br />
				<input type="text" onblur="valemdest();" id="emaildest" name="emaildest" value="<?php echo $data['emaildest']; ?>" />
			</div>
		</div>

		<div id="fields-address" class="formfields">
			<div class="field">
				<label for="adress"><?php echo Text::get('invest-address-address-field') ?> *</label><span class="error" id="addressi"></span><br />
				<input type="text" onblur="vfield('address');" id="address" name="address" value="<?php echo $data['address']; ?>" />
			</div>

			<div class="field">
				<label for="location"><?php echo Text::get('invest-address-location-field') ?> *</label><span class="error" id="locationi"></span><br />
				<input type="text" onblur="vfield('location');" id="location" name="location" value="<?php echo $data['location']; ?>" />
			</div>

			<div class="field">
				<label for="country"><?php echo Text::get('invest-address-country-field') ?> *</label><span class="error" id="countryi"></span><br />
				<input type="text" onblur="vfield('country');" id="country" name="country" value="<?php echo $data['country']; ?>" />
			</div>

			<div class="field">
				<label for="zipcode"><?php echo Text::get('invest-address-zipcode-field') ?> *</label><span class="error" id="zipcodei"></span><br />
				<input type="text" onblur="vfield('zipcode');" id="zipcode" name="zipcode" value="<?php echo $data['zipcode']; ?>" />
			</div>
		</div>

		<div id="field-message" class="formfields friend"<?php if ($data['regalo']) echo ' style="display:block;"'; ?>>
			<label for="message"><?php echo Text::get('invest-address-msgdest-field') ?></label><br />
			<textarea rows="5" id="message" name="message"><?php echo $data['message']; ?></textarea>
		</div>

		<div id="anonm" class="formfields alone">
			<label for="anonymous"><input type="checkbox" id="anonymous" name="anonymous" value="1" <?php if ($data['anonymous']) echo 'checked="checked"'; ?>/><?php echo Text::get('invest-anonymous') ?></label><br />
		</div>

		<div class="buttons">
		    <button type="submit" class="process pay-tpv" name="method"  value="tpv">TPV</button>
		    <?php if ($allowpp) : ?><button type="submit" class="process pay-paypal" name="method"  value="paypal">PAYPAL</button><?php endif; ?>
		    <?php if ($debug) : ?><button type="submit" class="process pay-cash" name="method"  value="cash">CASH</button><?php endif; ?>
		</div>

	</form>

    <?php if (!$allowpp) : ?><div class="reminder"><?php echo Text::html('invest-paypal_disabled') ?></div><?php endif; ?>


</article>
