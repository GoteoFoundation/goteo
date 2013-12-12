<?php
	use Goteo\Library\Text,
	    Goteo\Model\Invest,
	    Goteo\Model\Call;


	$item = $this['item'];
//	$project = $this['project'];

	$debug = true;
    $allowpp = true;

	//@TODO si el usuario está logueado no mostramos los campos de nombre y email 
	//@TODO textos dinamicos
	//@TODO botones paypal y tpv (como en la página de aportar)
	//@TODO preseleccion de regalo / anonimo
	//@TODO pasar a jquery después de maquetar

/*
if ($project->called instanceof Call && $project->called->dropable) {
    $allowpp = false;
} else {
    $allowpp = $project->allowpp;
}
*/


?>
<script type="text/javascript">

function validar(){
	var n=valnombre();
	var e=valemail();
	var d=valnomdest();
	var f=valemdest();
	var l=valdir();
	var c=valciud();
	var p=valcp();
	var a=valpais();
	if(n==false || e==false || d==false || f==false || l==false || c==false || p==false || a==false){
		return false;
	} else {
		return true;
	}
}
function valnombre(){
				var nam=document.getElementById("name").value;
				if(nam==null || nam.length==0){
					document.getElementById("namei").innerHTML="Obligatorio";
					return false;
				}else{
					document.getElementById("namei").innerHTML="";
					return true;
				}
				
  }
function valemail(){
				var em=document.getElementById("email").value;
				if(em==null || em.length==0){
					document.getElementById("emaili").innerHTML="Obligatorio";
					return false;
				}else if(!(/^\w+@[a-z]+\.[a-z]+$/.test(em))){
					document.getElementById("emaili").innerHTML="E-mail no válido";
					return false;
				}else{
					document.getElementById("emaili").innerHTML="";
					return true;
				}
				
  }
function valnomdest(){
	var namdest=document.getElementById("namedest").value;
	var check = document.getElementById("check");
        if (check.checked) {
			if(namdest==null || namdest.length==0){
				document.getElementById("namedesti").innerHTML="Obligatorio";
				return false;
			}else{
				document.getElementById("namedesti").innerHTML="";
				return true;
			}
		} else {
			document.getElementById("namedesti").innerHTML="";
			return true;
		}
}
function valemdest(){
	var check = document.getElementById("check");
        if (check.checked) {
			var emdest=document.getElementById("emaildest").value;
			if(emdest==null || emdest.length==0){
				document.getElementById("emaildesti").innerHTML="Obligatorio";
				return false;
			}else if(!(/^\w+@[a-z]+\.[a-z]+$/.test(emdest))){
				document.getElementById("emaildesti").innerHTML="Formato de E-mail invalido";
				return false;
			}else{
				document.getElementById("emaildesti").innerHTML="";
				return true;
			}
		} else {
			document.getElementById("emaildesti").innerHTML="";
			return true;
		}
}
function valdir(){
				var dir=document.getElementById("address").value;
				if(dir==null || dir.length==0){
					document.getElementById("addressi").innerHTML="Obligatorio";
					return false;
				}else{
					document.getElementById("addressi").innerHTML="";
					return true;
				}
				
  }
function valciud(){
				var ciud=document.getElementById("location").value;
				if(ciud==null || ciud.length==0){
					document.getElementById("locationi").innerHTML="Obligatorio";
					return false;
				}else{
					document.getElementById("locationi").innerHTML="";
					return true;
				}
				
  }
function valpais(){
				var pais=document.getElementById("country").value;
				if(pais==null || pais.length==0){
					document.getElementById("countryi").innerHTML="Obligatorio";
					return false;
				}else{
					document.getElementById("countryi").innerHTML="";
					return true;
				}
				
  }
function valcp(){
				var cp=document.getElementById("zipcode").value;
				if(cp==null || cp.length==0){
					document.getElementById("zipcodei").innerHTML="Obligatorio";
					return false;
				}else{
					document.getElementById("zipcodei").innerHTML="";
					return true;
				}
				
  }
   function showContent() {
        check = document.getElementById("check");
        if (check.checked) {
        	$(".friend").show();
        }
        else {
			document.getElementById("namedesti").innerHTML="";
			document.getElementById("emaildesti").innerHTML="";
        	$(".friend").hide();
        }
    }
</script>

<section id="formulario">

	<form method="post" action="/bazaar/pay/<?php echo $item->id; ?>" onsubmit="return validar();" >

		<div id="sendto" class="formfields alone">
			<label><input type="checkbox" onchange="showContent();" id="check" name="regalo" />Es un regalo</label>
		</div>

		<div id="fields-investor" class="formfields">
			<div class="field">
				<label for="name">Tu Nombre *</label><span class="error"  id="namei"></span><br />
				<input type="text" onblur="valnombre();" id="name" name="name" value="<?php echo $_SESSION['bazar']['name']; ?>" />
			</div>

			<div class="field">
				<label for="email">Tu E-mail *</label><span class="error" id="emaili"></span><br />
				<input type="text" onblur="valemail();" id="email" name="email" value="<?php echo $_SESSION['bazar']['email']; ?>" />
			</div>
		</div>

		<div id="fields-address" class="formfields">
			<div class="field">
				<label for="adress">Direccion de Envio *</label><span class="error" id="addressi"></span><br />
				<input type="text" onblur="valdir();" id="address" name="address" value="<?php echo $_SESSION['bazar']['address']; ?>" />
			</div>

			<div class="field">
				<label for="location">Ciudad *</label><span class="error" id="locationi"></span><br />
				<input type="text" onblur="valciud();" id="location" name="location" value="<?php echo $_SESSION['bazar']['location']; ?>" />
			</div>

			<div class="field">
				<label for="country">Pais *</label><span class="error" id="countryi"></span><br />
				<input type="text" onblur="valpais();" id="country" name="country" value="<?php echo $_SESSION['bazar']['country']; ?>" />
			</div>

			<div class="field">
				<label for="zipcode">Codigo Postal *</label><span class="error" id="zipcodei"></span><br />
				<input type="text" onblur="valcp();" id="zipcode" name="zipcode" value="<?php echo $_SESSION['bazar']['zipcode']; ?>" />
			</div>
		</div>

		<div id="fields-friend" class="formfields friend">
			<div class="field">
				<label for="namedest">Nombre del Destinatario</label><span class="error" id="namedesti"></span><br />
				<input type="text" onblur="valnomdest();" id="namedest" name="namedest" value="<?php echo $_SESSION['bazar']['namedest']; ?>" />
			</div>

			<div class="field">
				<label for="emaildest">E-mail del Destinatario</label><span class="error" id="emaildesti"></span><br />
				<input type="text" onblur="valemdest();" id="emaildest" name="emaildest" value="<?php echo $_SESSION['bazar']['emaildest']; ?>" />
			</div>
		</div>

		<div id="field-message" class="formfields friend">
			<label for="message">Mensaje para el destinatario:</label><br />
			<textarea rows="5" id="message" name="message"><?php echo $_SESSION['bazar']['message']; ?></textarea>
		</div>

		<div id="anonm" class="formfields alone">
			<label for="anonymous"><input type="checkbox" id="anonymous" name="anonymous" />Aporte Anonimo</label><br />
		</div>

		<div class="buttons">
		    <button type="submit" class="process pay-tpv" name="method"  value="tpv">TPV</button>
		    <?php if ($allowpp) : ?><button type="submit" class="process pay-paypal" name="method"  value="paypal">PAYPAL</button><?php endif; ?>
		    <?php if ($debug) : ?><button type="submit" class="process pay-cash" name="method"  value="cash">CASH</button><?php endif; ?>
		</div>

	</form>

</section>