<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
USUARIO / Datos personales<br />
GUÍA<br />
<?php include 'view/project/errors.html.php' ?>
FORMULARIO <br />
<form action="/project/register" method="post">
	<dl>
		<dt><label for="contract_name">Nombre</label></dt>
		<dd><input type="text" id="contract_name" name="contract_name" value="<?php echo $project->contract_name; ?>"/></dd>

		<dt><label for="contract_surname">Apellidos</label></dt>
		<dd><input type="text" id="contract_surname" name="contract_surname" value="<?php echo $project->contract_surname; ?>"/></dd>

		<dt><label for="contract_nif">NIF</label></dt>
		<dd><input type="text" id="contract_nif" name="contract_nif" value="<?php echo $project->contract_nif; ?>"/></dd>

		<dt><label for="contract_email">Email</label></dt>
		<dd><input type="text" id="contract_email" name="contract_email" value="<?php echo $project->contract_email; ?>"/></dd>

		<dt><label for="phone">Teléfono</label></dt>
		<dd><input type="text" id="phone" name="phone" value="<?php echo $project->phone; ?>"/></dd>

		<dt><label for="address">Dirección</label></dt>
		<dd><input type="text" id="address" name="address" value="<?php echo $project->address; ?>"/></dd>

		<dt><label for="zipcode">Código postal</label></dt>
		<dd><input type="text" id="zipcode" name="zipcode" value="<?php echo $project->zipcode; ?>"/></dd>

		<dt><label for="location">Lugar de residencia</label></dt>
		<dd><input type="text" id="location" name="location" value="<?php echo $project->location; ?>"/></dd>

		<dt><label for="country">País</label></dt>
		<dd><input type="text" id="country" name="country" value="<?php echo $project->country; ?>"/></dd>

	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>