<?php
	use Goteo\Library\Text,
	    Goteo\Model\Invest,
	    Goteo\Model\Call;

$item = $this['item'];
$project = $this['project'];


	//@TODO si el usuario está logueado no mostramos los campos de nombre y email 
	//@TODO textos dinamicos
	//@TODO botones paypal y tpv (como en la página de aportar) (cash para pruebas)
	//@TODO preseleccion de regalo / anonimo
	//@TODO jquery de verificación y de regalo/parami
	//@TODO tema riego
// verificar si puede obtener riego
if ($project->called instanceof Call && $project->called->dropable) {
    $allowpp = false;
} else {
    $allowpp = $project->allowpp;
}


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
	}
}
function valnombre(){
				var nam=document.getElementById("name").value;
				if(nam==null || nam.length==0){
					document.getElementById("namei").innerHTML="<p>Obligatorio</p>";
					return false;
				}else{
					return true;
				}
				
  }
function valemail(){
				var em=document.getElementById("email").value;
				if(em==null || em.length==0){
					document.getElementById("emaili").innerHTML="<p>Obligatorio</p>";
					return false;
				}else if(!(/^\w+@[a-z]+\.[a-z]+$/.test(em))){
		document.getElementById("emaili").innerHTML="<p>E-mail no válido</p>";
		return false;}else{
					return true;
				}
				
  }
function valnomdest(){
	var namdest=document.getElementById("namedest").value;
				if(namdest==null || namdest.length==0){
					document.getElementById("namedesti").innerHTML="<p>Obligatorio</p>";
					return false;
				}else{
					return true;
				}
}
function valemdest(){
				var emdest=document.getElementById("emaildest").value;
				if(emdest==null || emdest.length==0){
					document.getElementById("emaildesti").innerHTML="<p>Obligatorio</p>";
					return false;
				}else if(!(/^\w+@[a-z]+\.[a-z]+$/.test(emdest))){
		document.getElementById("emaildesti").innerHTML="<p>Formato de E-mail invalido</p>";
		return false;}else{
					return true;
				}
				
  }
function valdir(){
				var dir=document.getElementById("address").value;
				if(dir==null || dir.length==0){
					document.getElementById("addressi").innerHTML="<p>Obligatorio</p>";
					return false;
				}else{
					return true;
				}
				
  }
function valciud(){
				var ciud=document.getElementById("location").value;
				if(ciud==null || ciud.length==0){
					document.getElementById("locationi").innerHTML="<p>Obligatorio</p>";
					return false;
				}else{
					return true;
				}
				
  }
function valpais(){
				var pais=document.getElementById("country").value;
				if(pais==null || pais.length==0){
					document.getElementById("countryi").innerHTML="<p>Obligatorio</p>";
					return false;
				}else{
					return true;
				}
				
  }
function valcp(){
				var cp=document.getElementById("zipcode").value;
				if(cp==null || cp.length==0){
					document.getElementById("zipcodei").innerHTML="<p>Obligatorio</p>";
					return false;
				}else{
					return true;
				}
				
  }
   function showContent() {
        element = document.getElementById("campo3");
        check = document.getElementById("check");
        if (check.checked) {
            element.style.display='block';
        }
        else {
            element.style.display='none';
        }
    }
</script>

<form method="post" action="/bazaar/<?php echo $this['id']; ?>">

<div id="regalo">
	<label><input type="checkbox" onchange="showContent();" id="check" name="regalo" />Es un regalo</label>
</div>

<div id="campo1">
	<label for="fullname">Tu Nombre</label><br />
	<input type="text" onblur="valnombre();" id="name" name="name" value="<?php echo $_SESSION['bazar']['name']; ?>" /><p id="namei"></p>

	<label for="email">Tu E-mail</label><br />
	<input type="text" onblur="valemail();" id="email" name="email" value="<?php echo $_SESSION['bazar']['email']; ?>" /><p id="emaili"></p>
</div>

<div id="campo2">
	<label for="adress">Direccion de Envio</label><br />
	<input type="text" onblur="valdir();" id="address" name="address" value="<?php echo $_SESSION['bazar']['address']; ?>" /><p id="addressi"></p>

	<label for="location">Ciudad</label><br />
	<input type="text" onblur="valciud();" id="location" name="location" value="<?php echo $_SESSION['bazar']['location']; ?>" /><p id="locationi"></p>

	<label for="country">Pais</label><br />
	<input type="text" onblur="valpais();" id="country" name="country" value="<?php echo $_SESSION['bazar']['country']; ?>" /><p id="countryi"></p>

	<label for="zipcode">Codigo Postal</label><br />
	<input type="text" onblur="valcp();" id="zipcode" name="zipcode" value="<?php echo $_SESSION['bazar']['zipcode']; ?>" /><p id="zipcodei"></p>
</div>

<div id="campo3">
	<label for="namedest">Nombre del Destinatario</label><br />
	<input type="text" onblur="valnomdest();" id="namedest" name="namedest" value="<?php echo $_SESSION['bazar']['namedest']; ?>" /><p id="namedesti"></p>

	<label for="emaildest">E-mail del Destinatario</label><br />
	<input type="text" onblur="valemdest();" id="emaildest" name="emaildest" value="<?php echo $_SESSION['bazar']['emaildest']; ?>" /><p id="emaildesti"></p>
</div>

<div id="campo4">
	<label for="message">Mensaje para el destinatario:</label><br />
	<textarea cols="20" rows="5" id="message" name="message"><?php echo $_SESSION['bazar']['message']; ?></textarea>
</div>

<div id="campo5">
	<label for="anonymous"><input type="checkbox" id="anonymous" name="anonymous" />Aporte Anonimo</label><br />
</div>

<div class="buttons">
    <button type="submit" class="process pay-tpv" name="method"  value="tpv">TPV</button>
    <?php if ($allowpp) : ?><button type="submit" class="process pay-paypal" name="method"  value="paypal">PAYPAL</button><?php endif; ?>
</div>

<input type="submit" name="method" value="Pagar" />
</form>