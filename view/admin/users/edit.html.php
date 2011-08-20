<?php

use Goteo\Library\Text;

$user = $this['user'];

// aqui se editan los mismos datos personales que en el dashboard

?>
<!-- <span style="font-style:italic;font-weight:bold;">Atención! Le llegará email de verificación al usuario como si se hubiera registrado.</span> -->
<div class="widget">
    <form action="/admin/users/edit/<?php echo $user->id ?>" method="post">
        <!--
        <p>
            <label for="user-user">Login:</label><span style="font-style:italic;">Solo letras, números y guion '-'. Sin espacios ni tildes ni 'ñ' ni 'ç' ni otros caracteres que no sean letra, número o guión.</span><br />
            <input type="text" id="user-user" name="user" value="" size="50" maxlength="50"/>
        </p>
        <p>
            <label for="user-name">Nombre público:</label><br />
            <input type="text" id="user-name" name="name" value="" size="255" maxlength="255"/>
        </p>
        <p>
            <label for="user-email">Email:</label><span style="font-style:italic;">Que sea válido.</span><br />
            <input type="text" id="user-email" name="email" value="" size="255" maxlength="255"/>
        </p>
        <p>
            <label for="user-email">Contraseña:</label><span style="font-style:italic;">Se va a encriptar y no se puede consultar</span><br />
            <input type="text" id="user-name" name="name" value="" size="255" maxlength="255"/>
        </p>
-->
        <input type="submit" name="edit" value="Grabar" />

    </form>
</div>