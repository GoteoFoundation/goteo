<?php

$user = $this->user;
$roles = $this->roles;
$node_roles = array_intersect_key($user->getAllNodeRoles(), $this->admin_nodes);
$role_node_info = array();
foreach($node_roles as $n => $roles) {
    $role_node_info[] = $n .' [' . implode(' ', $roles) . ']';
}

?>
 <table style="width:100%">
 <?php $this->section('admin-user-info') ?>
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
        <td><?php echo $this->admin_nodes[$user->node] ?></td>
    </tr>
    <tr>
        <td>Roles actuales</td>
        <td><?= implode('<br>', $role_node_info) ?></td>
    </tr>

<?php $this->stop() ?>
</table>
