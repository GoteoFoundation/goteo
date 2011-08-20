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
        <dd><?php echo implode(', ', $roles); ?></dd>
    </dl>
    <dl>
        <dt>Estado de la cuenta:</dt>
        <dd><strong><?php echo $user->active ? 'Activa' : 'Inactiva'; ?></strong></dd>
    </dl>

    <p>
    <?php if ($user->active) : ?>
        <a href="<?php echo "/admin/users/manage/{$user->id}/ban"; ?>" class="button weak">Desactivar</a>
    <?php else : ?>
        <a href="<?php echo "/admin/users/manage/{$user->id}/unban"; ?>" class="button">Activar</a>
    <?php endif; ?>

    <?php if (in_array('checker', array_keys($user->roles))) : ?>
        <a href="<?php echo "/admin/users/manage/{$user->id}/nochecker"; ?>" class="button weak">Quitarlo de revisor</a>
    <?php else : ?>
        <a href="<?php echo "/admin/users/manage/{$user->id}/checker"; ?>" class="button">Hacerlo revisor</a>
    <?php endif; ?>

    <?php if (in_array('translator', array_keys($user->roles))) : ?>
        <a href="<?php echo "/admin/users/manage/{$user->id}/notranslator"; ?>" class="button weak">Quitarlo de traductor</a>
    <?php else : ?>
        <a href="<?php echo "/admin/users/manage/{$user->id}/translator"; ?>" class="button">Hacerlo traductor</a>
    <?php endif; ?>

    <!--
    <?php # if (in_array('admin', array_keys($user->roles))) : ?>
        <a href="<?php # echo "/admin/users/manage/{$user->id}/noadmin"; ?>" class="button weak">Quitarlo de admin</a>
    <?php # else : ?>
        <a href="<?php # echo "/admin/users/manage/{$user->id}/admin"; ?>" class="button">Hacerlo admin</a>
    <?php # endif; ?>
    -->

    </p>
</div>

