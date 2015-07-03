<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$user = $this->user;
$roles = $this->roles;
$node_roles = array_intersect_key($user->getAllNodeRoles(), $this->admin_nodes);
$role_node_info = array();
foreach($node_roles as $n) {
    $role_node_info[] = $n->node .': ' .$user->roles[$n->role]->name;
}
$parts = explode('/', $this->template);
$part = end($parts);

?>
<div class="widget board">
<ul class="ul-admin">
    <li<?= ($part === 'manage' ? ' class="selected"' : '') ?>><a href="/admin/users/manage/<?php echo $user->id; ?>">[Admin. roles]</a></li>
    <li<?= ($part === 'edit' ? ' class="selected"' : '') ?>><a href="/admin/users/edit/<?php echo $user->id; ?>">[Email/contraseña]</a></li>
    <li<?= ($part === 'move' ? ' class="selected"' : '') ?>><a href="/admin/users/move/<?php echo $user->id; ?>">[Mover de   Nodo]</a></li>
    <li<?= ($part === 'impersonate' ? ' class="selected"' : '') ?>><a href="/admin/users/impersonate/<?php echo $user->id; ?>">[Suplantar]</a></li>
    <?php if ($this->is_module_admin('Accounts', $this->admin_node)) : ?>
        <li><a href="/admin/accounts/add?user=<?php echo $user->id; ?>">[Crear aporte]</a></li>
        <li><a href="/admin/accounts?name=<?php echo $user->email; ?>">[Historial aportes]</a></li>
    <?php else: ?>
        <li><a href="/admin/invests?name=<?php echo $user->email; ?>">[Historial aportes]</a></li>
    <?php endif ?>
    <li><a href="/admin/sent?user=<?php echo urlencode($user->email); ?>">[Historial envíos]</a></li>
</ul>
</div>

<div class="widget">
     <?= $this->insert('admin/users/partials/user_info') ?>
</div>

<div class="widget board">
    <?= $this->supply('admin-user-board') ?>
</div>

<?php $this->replace() ?>

