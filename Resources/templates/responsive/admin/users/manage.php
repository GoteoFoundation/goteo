<?php

$this->layout('admin/container');

$this->section('admin-container-head');
?>
    <h2><?= $this->text('admin-users') ?></h2>

<?php
$this->replace();
$this->section('admin-container-body');
?>
<div class="row">
    <ul class="admin-list col-sm-6 col-xs-12">
        <li><strong><?= $this->text('admin-title-id') ?>:</strong> <?= $this->user->id ?></li>
        <li><strong><?= $this->text('admin-title-name') ?>:</strong> <?= $this->user->name ?> <button title="<?= $this->text('regular-edit') ?>"><i class="fa fa-edit"></i></button></li>
        <li><a href="/admin/users/impersonate/<?= $this->user->id ?>"><i class="fa fa-user-md"></i> <?= $this->text('admin-impersonate') ?></a></li>
        <li><a href="/user/profile/<?= $this->user->id ?>" target="_blank"><i class="fa fa-external-link"></i> <?= $this->text('dashboard-menu-profile-public') ?></a></li>
        <li><?php if($projects = $this->user->getProjectNames()): ?>
                <strong><?= $this->text('admin-projects') ?>:</strong><br>
                <?php foreach($projects as $id => $p): ?>
                    <a href="/project/<?= $id ?>" target="_blank"><i class="fa fa-external-link"></i> <?= $p ?></a><br>
                <?php endforeach ?>
            <?php else: ?>
                <strong><?= $this->text('admin-user-no-projects') ?></strong>
            <?php endif ?>
        </li>
    </ul>

    <ul id="role-list" class="collapsable-list admin-list col-sm-6 col-xs-12">
        <li><strong><?= $this->text('admin-title-roles') ?>:</strong> <button title="<?= $this->text('regular-edit') ?>"><i class="fa fa-edit"></i></button></li>
    <?php
    $roles = $this->user->getRoles();
    foreach($roles as $role => $perms): ?>
        <li>
            <a class="collapsable" href="#collapse-<?= $this->user->id . '-' . $role ?>" data-toggle="collapse" data-parent="#role-list"><?= $this->insert('admin/partials/objects/roles', ['value' => [$role => $roles::getRoleName($role)]]) ?></a>
            <ul class="collapse admin-list" id="collapse-<?= $this->user->id . '-' . $role ?>">
                <?php foreach($perms as $perm): ?>
                    <li><?= $roles::getPermName($perm) ?></li>
                <?php endforeach ?>
            </ul>
        </li>
    <?php endforeach ?>
    </ul>
</div>
<?php $this->replace() ?>
