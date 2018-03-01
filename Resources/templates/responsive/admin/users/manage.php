<?php

$this->layout('admin/container');

$this->section('admin-container-head');
?>
    <h2><?= $this->text('admin-users') ?></h2>

<?php
$this->replace();

$this->section('admin-container-body');
$id = $this->user->id;
$editable = $this->get_user()->canImpersonate($this->user) ? 'editable editable-click' : '';

$roles = $this->user->getRoles();
$role_names = $roles->getRoleNames();
$all_roles = $roles::getAllRoleNames();
?>
<div class="row">
  <div class="col-sm-6 col-xs-12">
    <ul class="admin-list">
        <li>
            <strong><?= $this->text('admin-title-id') ?>:</strong>
            <span title="<?= $this->text('admin-rebase-user') ?>" data-url="/api/users/<?= $id ?>/property/id" data-name="value" data-pk="<?= $id ?>" class="<?= $editable ?>"><?= $id ?></span>
        </li>
        <li>
            <strong><?= $this->text('admin-title-email') ?>:</strong>
            <span title="<?= $this->text('admin-rebase-user') ?>" data-url="/api/users/<?= $id ?>/property/email" data-name="value" data-pk="<?= $id ?>" data-type="email" class="<?= $editable ?>"><?= $this->user->email ?></span>
        </li>
        <li>
            <strong><?= $this->text('admin-title-name') ?>:</strong>
            <span id="name-<?= $id ?>" title="<?= $this->text('admin-edit-name') ?>" data-url="/api/users/<?= $id ?>/property/name" data-name="value" data-pk="<?= $id ?>" class="<?= $editable ?>"><?= $this->user->name ?></span>
            <?php if($editable): ?>
              <button class="editable" data-target="#name-<?= $id ?>" title="<?= $this->text('regular-edit') ?>"><i class="fa fa-edit"></i></button>
            <?php endif ?>
        </li>
        <?php if($editable): ?>
          <li>
            <a href="/admin/users/impersonate/<?= $id ?>" target="_blank"><i class="fa fa-user-md"></i> <?= $this->text('admin-impersonate') ?></a>
          </li>
        <?php endif ?>
    </ul>

    <ul id="role-list" class="collapsable-list admin-list">
        <li>
            <strong><?= $this->text('admin-title-roles') ?>:</strong>
            <?php if($editable): ?>
              <button class="<?= $editable ?> edit-roles" title="<?= $this->text('admin-edit-roles') ?>" data-target="#roles-<?= $id ?>"><i class="fa fa-edit"></i></button>
              <br>
              <span id="roles-<?= $id ?>" data-type="checklist" data-value="<?= implode(', ', array_keys($role_names)) ?>" data-source="<?= $this->ee(json_encode($all_roles)) ?>" data-url="/api/users/<?= $id ?>/property/roles" data-name="value" data-pk="<?= $id ?>" data-placement="auto left" class="<?= $editable ?>"><?= implode("<br>\n", $role_names) ?></span>
            <?php endif ?>
            <button data-toggle="modal" data-target="#admin-modal" data-url="/admin/users/roles?ajax&pronto=true" data-title="<?= $this->text('admin-user-roles') ?>" title="<?= $this->text('admin-show-roles-info') ?>"><i class="fa fa-question"></i></button>

        </li>
    </ul>
  </div>

  <div class="col-sm-6 col-xs-12">
    <ul class="admin-list">
        <li>
            <a href="/user/profile/<?= $id ?>" target="_blank"><i class="fa fa-external-link"></i> <?= $this->text('dashboard-menu-profile-public') ?></a>
        </li>
        <li>
            <a href="mailto:<?= $this->user->email ?>"><i class="fa fa-at"></i> <?= $this->text('admin-send-email') ?></a>
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
