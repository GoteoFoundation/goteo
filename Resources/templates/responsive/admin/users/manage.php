<?php

$this->layout('admin/container');

$this->section('admin-container-head');
?>
    <h2><?= $this->text('admin-users') ?></h2>

<?php
$this->replace();

$this->section('admin-container-body');
$id = $this->user->id;
?>
<div class="row">
  <div class="col-sm-6 col-xs-12">
    <ul class="admin-list">
        <li>
            <strong><?= $this->text('admin-title-id') ?>:</strong>
            <span title="<?= $this->text('admin-rebase-user') ?>" class="editable editable-click"><?= $id ?></span>
        </li>
        <li>
            <strong><?= $this->text('admin-title-name') ?>:</strong>
            <span id="name-<?= $id ?>" title="<?= $this->text('admin-edit-name') ?>" class="editable editable-click"><?= $this->user->name ?></span>
            <button class="editable" data-target="#name-<?= $id ?>" title="<?= $this->text('regular-edit') ?>"><i class="fa fa-edit"></i></button>
        </li>
        <?php if($this->get_user()->canImpersonate($this->user)): ?>
          <li>
            <a href="/admin/users/impersonate/<?= $id ?>" target="_blank"><i class="fa fa-user-md"></i> <?= $this->text('admin-impersonate') ?></a>
          </li>
        <?php endif ?>
    </ul>

    <?php
        $roles = $this->user->getRoles();
        $role_names = $roles->getRoleNames();
        $all_roles = $roles::getAllRoleNames();
    ?>
    <ul id="role-list" class="collapsable-list admin-list">
        <li>
            <strong><?= $this->text('admin-title-roles') ?>:</strong>
            <button class="editable edit-roles" title="<?= $this->text('admin-edit-roles') ?>" data-target="#roles-<?= $id ?>"><i class="fa fa-edit"></i></button>
            <br>
            <span id="roles-<?= $id ?>" data-type="checklist" data-value="<?= implode(', ', array_keys($role_names)) ?>" data-source="<?= $this->ee(json_encode($all_roles)) ?>" data-placement="auto left" class="editable editable-click"><?= implode("<br>\n", $role_names) ?></span>
        </li>
    <?php foreach($roles as $role => $perms): ?>
        <li>
            <a class="collapsable" href="#collapse-<?= $id . '-' . $role ?>" data-toggle="collapse" data-parent="#role-list"><?= $this->insert('admin/partials/objects/roles', ['value' => [$role => $role_names[$role]]]) ?></a>
            <ul class="collapse admin-list" id="collapse-<?= $id . '-' . $role ?>">
                <?php foreach($perms as $perm): ?>
                    <li><?= $roles::getPermName($perm) ?></li>
                <?php endforeach ?>
            </ul>
        </li>
    <?php endforeach ?>
    </ul>
  </div>

  <div class="col-sm-6 col-xs-12">
    <ul class="admin-list">
        <li>
            <a href="/user/profile/<?= $id ?>" target="_blank"><i class="fa fa-external-link"></i> <?= $this->text('dashboard-menu-profile-public') ?></a>
        </li>
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


  </div>
</div>
<?php $this->replace() ?>
