<?php

use Goteo\Library\Text;

?>
<div class="widget">
    <form action="/admin/users/add" method="post">
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
            <label for="user-password">Contraseña:</label><span style="font-style:italic;">Se va a encriptar y no se puede consultar</span><br />
            <input type="text" id="user-password" name="password" value="" size="255" maxlength="255"/>
        </p>

        <input type="submit" name="add" value="Crear este usuario" /><br />
        <span style="font-style:italic;font-weight:bold;">Atención! Le llegará email de verificación al usuario como si se hubiera registrado.</span>

    </form>
</div>