<?php

use Goteo\Library\Text;

$data = $vars['data'];
$user = $vars['user'];

$roles = $user->roles;
array_walk($roles, function (&$role) { $role = $role->name; });
?>
<div class="widget">
    <dl>
        <dt>Nombre de usuario:</dt>
        <dd><?php echo $user->name ?></dd>
    </dl>
    <dl>
        <dt>Login de acceso:</dt>
        <dd><strong><?php echo $user->id ?></strong></dd>
    </dl>
    <dl>
        <dt>Email:</dt>
        <dd><?php echo $user->email ?></dd>
    </dl>
    <dl>
        <dt>Nodo:</dt>
        <dd><?php echo $vars['nodes'][$user->node] ?></dd>
    </dl>
    <dl>
        <dt>Roles actuales:</dt>
        <dd><?php echo implode(', ', $roles); ?></dd>
    </dl>
    <dl>
        <dt>Estado de la cuenta:</dt>
        <dd><strong><?php echo $user->active ? 'Activa' : 'Inactiva'; ?></strong></dd>
    </dl>

    <form action="/admin/users/edit/<?php echo $user->id ?>" method="post">
        <p>
            <label for="user-email">Email:</label><span style="font-style:italic;">Que sea válido. Se verifica que no esté repetido</span><br />
            <input type="text" id="user-email" name="email" value="<?php echo $data['email'] ?>" style="width:500px" maxlength="255"/>
        </p>
        <p>
            <label for="user-password">Contraseña:</label><span style="font-style:italic;">Mínimo 6 caracteres. Se va a encriptar y no se puede consultar</span><br />
            <input type="text" id="user-password" name="password" value="<?php echo $data['password'] ?>" style="width:500px" maxlength="255"/>
        </p>

        <input type="submit" name="edit" value="Actualizar"  onclick="return confirm('Entiendes que vas a cambiar datos críticos de la cuenta de este usuario?');"/><br />
        <span style="font-style:italic;font-weight:bold;color:red;">Atención! Se están substituyendo directamente los datos introducidos, no habrá email de autorización.</span>

    </form>
</div>
