<?php

use Goteo\Library\Text;

$user = $vars['user'];

$roles = $user->roles;
array_walk($roles, function (&$role) { $role = $role->name; });
?>
<div class="widget">
    <dl>
        <dt>Nombre de usuario</dt>
        <dd><?php echo $user->name ?></dd>
    </dl>
    <dl>
        <dt>Login de acceso</dt>
        <dd><strong><?php echo $user->id ?></strong></dd>
    </dl>
    <dl>
        <dt>Email</dt>
        <dd><?php echo $user->email ?></dd>
    </dl>
    <dl>
        <dt>Nodo:</dt>
        <dd><?php echo $vars['nodes'][$user->node] ?></dd>
    </dl>
    <dl>
        <dt>Roles actuales</dt>
        <dd><?php echo implode(', ', $roles); ?></dd>
    </dl>

    <form action="/impersonate" method="post">
        <input type="hidden" name="id" value="<?php echo $user->id ?>" />

        <input type="submit" class="red" name="impersonate" value="Suplantar a este usuario" onclick="return confirm('Estás completamente seguro de entender lo que esás haciendo?');" /><br />
        <span style="font-style:italic;font-weight:bold;color:red;">Atención!! Con esto vas a dejar de estar logueado como el superadmin que eres y pasarás a estar logueado como este usuario con todos sus permisos y restricciones.</span>

    </form>
</div>
