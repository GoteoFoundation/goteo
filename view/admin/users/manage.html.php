<?php

use Goteo\Library\Text;

$user = $this['user'];

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
        <dt>Roles actuales:</dt>
        <dd>
            <?php echo implode(', ', $roles); ?><br />
            <?php if (in_array('checker', array_keys($user->roles))) : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/nochecker"; ?>" class="button red">Quitarlo de revisor</a>
            <?php else : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/checker"; ?>" class="button">Hacerlo revisor</a>
            <?php endif; ?>

            <?php if (in_array('translator', array_keys($user->roles))) : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/notranslator"; ?>" class="button red">Quitarlo de traductor</a>
            <?php else : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/translator"; ?>" class="button">Hacerlo traductor</a>
            <?php endif; ?>

            <?php if (in_array('caller', array_keys($user->roles))) : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/nocaller"; ?>" class="button red">Quitarlo de convocador</a>
            <?php else : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/caller"; ?>" class="button">Hacerlo convocador</a>
            <?php endif; ?>

            <?php if (in_array('admin', array_keys($user->roles))) : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/noadmin"; ?>" class="button red">Quitarlo de admin</a>
            <?php else : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/admin"; ?>" class="button">Hacerlo admin</a>
            <?php endif; ?>
        </dd>
    </dl>
    <dl>
        <dt>Estado de la cuenta:</dt>
        <dd>
            <strong><?php echo $user->active ? 'Activa' : 'Inactiva'; ?></strong>
            <?php if ($user->active) : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/ban"; ?>" class="button">Desactivar</a>
            <?php else : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/unban"; ?>" class="button red">Activar</a>
            <?php endif; ?>
        </dd>
    </dl>
    <dl>
        <dt>Visibilidad:</dt>
        <dd>
            <strong><?php echo $user->hide ? 'Oculto' : 'Visible'; ?></strong>
            <?php if (!$user->hide) : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/hide"; ?>" class="button">Ocultar</a>
            <?php else : ?>
                <a href="<?php echo "/admin/users/manage/{$user->id}/show"; ?>" class="button red">Mostrar</a>
            <?php endif; ?>
        </dd>
    </dl>
    <!--
    <p>
        <a href="<?php echo "/admin/users/manage/{$user->id}/delete"; ?>" class="button weak" onclick="return confirm('Estas seguro de que quieres eliminar este usuario y todos sus registros asociados (proyectos, mensajes, aportes...)')">Eliminar</a>
    </p>
    -->

</div>

