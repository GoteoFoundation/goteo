<?php
$this->layout('admin/container');

$this->section('admin-container-head');
?>
    <h2><?= $this->text('admin-user-roles') ?></h2>

<?php
$this->replace();

$this->section('admin-container-body');

$role_names = $this->raw('role_names');
$roles = $this->raw('roles');


?>
<div id="all-roles-list">
<?php foreach($role_names as $role => $name): ?>
    <a class="collapsable" href="#collapse-role-<?= $role ?>" data-toggle="collapse" data-parent="#all-roles-list"><?= $this->insert('admin/partials/objects/roles', ['value' => [$role => $name]]) ?></a>
    <ul class="collapse admin-list" id="collapse-role-<?= $role ?>">
        <?php foreach($roles::getRolePerms($role) as $perm => $perm_name): ?>
            <li><?= $perm_name ?></li>
        <?php endforeach ?>
    </ul>
<?php endforeach ?>
</div>


<?php $this->replace() ?>
