<?php $this->layout('admin/users/view_layout') ?>

<?php $this->section('admin-user-board') ?>

<?php

$data = $this->data ? $this->data : [];
$user = $this->user;

?>
    <form action="/admin/users/edit/<?php echo $user->id ?>" method="post">
        <p>
            <label for="user-email">Email:</label><span style="font-style:italic;">Que sea válido. Se verifica que no esté repetido</span><br />
            <input type="text" id="user-email" name="email" value="<?php echo $data['email'] ?>" style="width:500px" maxlength="255"/>
        </p>
        <p>
            <label for="user-password">Contraseña:</label><span style="font-style:italic;">Mínimo 6 caracteres. Se va a encriptar y no se puede consultar</span><br />
            <input type="text" id="user-password" name="password" value="" style="width:500px" maxlength="255"/>
        </p>

        <input type="submit" name="edit" value="Actualizar"  onclick="return confirm('Entiendes que vas a cambiar datos críticos de la cuenta de este usuario?');"/><br />
        <span style="font-style:italic;font-weight:bold;color:red;">Atención! Se están substituyendo directamente los datos introducidos, no habrá email de autorización.</span>

    </form>


<?php $this->append() ?>
