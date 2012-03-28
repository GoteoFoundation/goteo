<?php

use Goteo\Library\Text,
    Goteo\Model\User;

$user = $this['user'];
$roles = User::getRolesList();
?>
<div class="widget">
    <table>
        <tr>
            <td width="140px">Nombre de usuario</td>
            <td><a href="/user/profile/<?php echo $user->id ?>" target="_blank"><?php echo $user->name ?></a></td>
        </tr>
        <tr>
            <td>Login de acceso</td>
            <td><strong><?php echo $user->id ?></strong></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?php echo $user->email ?></td>
        </tr>
        <tr>
            <td>Nodo</td>
            <td><?php echo $this['nodes'][$user->node] ?></td>
        </tr>
        <tr>
            <td>Roles actuales</td>
            <td>
                <?php
                foreach ($user->roles as $role=>$roleData) {
                    if (in_array($role, array('user', 'superadmin', 'root'))) {
                        echo '['.$roleData->name . ']&nbsp;&nbsp;';
                    } else {
                        // onclick="return confirm('Se le va a quitar el rol de <?php echo $roleData->name ? > a este usuario')"
                        ?>
                        [<a href="/admin/users/manage/<?php echo $user->id ?>/no<?php echo $role ?>" style="color:red;text-decoration:none;"><?php echo $roleData->name ?></a>]&nbsp;&nbsp;
                        <?php
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Roles disponibles</td>
            <td>
                <?php
                foreach ($roles as $roleId=>$roleName) {
                    if (!in_array($roleId, array_keys($user->roles)) && !in_array($roleId, array('root', 'superadmin'))) {
                        // onclick="return confirm('Se le va a dar el rol de <?php echo $roleName ? > a este usuario')"
                        ?>
                        <a href="/admin/users/manage/<?php echo $user->id ?>/<?php echo $roleId ?>" style="color:green;text-decoration:none;">[<?php echo $roleName ?>]</a>&nbsp;&nbsp;
                        <?php
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Estado de la cuenta</td>
            <td>
                <?php if ($user->active) : ?>
                    <a href="<?php echo "/admin/users/manage/{$user->id}/ban"; ?>" style="color:green;text-decoration:none;font-weight:bold;">Activa</a>
                <?php else : ?>
                    <a href="<?php echo "/admin/users/manage/{$user->id}/unban"; ?>" style="color:red;text-decoration:none;font-weight:bold;">Inactiva</a>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Visibilidad</td>
            <td>
                <?php if (!$user->hide) : ?>
                    <a href="<?php echo "/admin/users/manage/{$user->id}/hide"; ?>" style="color:green;text-decoration:none;font-weight:bold;">Visible</a>
                <?php else : ?>
                    <a href="<?php echo "/admin/users/manage/{$user->id}/show"; ?>" style="color:red;text-decoration:none;font-weight:bold;">Oculto</a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>
<div class="widget board">
    <ul>
        <li><a href="/admin/users/edit/<?php echo $user->id; ?>">[Cambiar email/contraseña]</a></li>
        <li><a href="/admin/users/move/<?php echo $user->id; ?>">[Mover a otro Nodo]</a></li>
        <li><a href="/admin/users/impersonate/<?php echo $user->id; ?>">[Suplantar]</a></li>
        <li><a href="/admin/<?php echo (isset($_SESSION['admin_node'])) ? 'invests' : 'accounts'; ?>/?users=<?php echo $user->id; ?>">[Historial aportes]</a></li>
        <li><a href="/admin/sended/?user=<?php echo $user->email; ?>">[Historial envíos]</a></li>
    </ul>




</div>

