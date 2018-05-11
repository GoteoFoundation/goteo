<?php

$this->layout('admin/users/layout');

$this->section('admin-search-box-addons');
?>
<a class="btn btn-cyan pull-right" href="/admin/users?<?= $this->get_querystring() ?>"><i class="fa fa-arrow-circle-left"></i> <?= $this->text('admin-back-list') ?></a>

<?php $this->replace() ?>

<?php
$this->section('admin-container-body');

$id = $this->user->id;
$editable_impersonate = $this->get_user()->canImpersonate($this->user) ? 'editable editable-click' : '';
$editable_rebase = $this->get_user()->canRebase($this->user) ? 'editable editable-click' : '';

$roles = $this->user->getRoles();
$role_names = $roles->getRoleNames();
$all_roles = $roles::getAllRoleNames();
?>

<h5 class="title"><?= $this->user->name ?>:</h5>

<div class="row">
  <div class="col-sm-6 col-xs-12">
    <h6 class="text-danger"><?= $this->text('admin-user-warning') ?>:</h6>
    <ul class="admin-list">
        <li>
            <strong><?= $this->text('admin-title-name') ?>:</strong>
            <span id="name-<?= $id ?>" title="<?= $this->text('admin-edit-name') ?>" data-url="/api/users/<?= $id ?>/property/name" data-name="value" data-pk="<?= $id ?>" class="<?= $editable_impersonate ?>"><?= $this->user->name ?></span>
        </li>
        <!--li>
            <strong><?= $this->text('admin-title-about') ?>:</strong>
            <span id="about-<?= $id ?>" title="<?= $this->text('admin-edit-about') ?>" data-url="/api/users/<?= $id ?>/property/about" data-name="value" data-type="textarea" data-pk="<?= $id ?>" class="<?= $editable_impersonate ?>"><?= $this->user->about ?></span>
        </li-->
        <li>
            <strong><?= $this->text('admin-title-location') ?>:</strong>
            <span id="location-<?= $id ?>" title="<?= $this->text('admin-edit-location') ?>" data-url="/api/users/<?= $id ?>/property/location" data-name="value" data-pk="<?= $id ?>" _class="<?= $editable_impersonate ?>"><?= $this->user->location ?></span>
        </li>
    </ul>
    <ul class="admin-list">
        <li>
            <strong><?= $this->text('admin-title-id') ?>:</strong>
            <span title="<?= $this->text('admin-rebase-user') ?>" data-url="/api/users/<?= $id ?>/property/id" data-name="value" data-pk="<?= $id ?>" class="<?= $editable_rebase ?>"><?= $id ?></span>
        </li>
        <li>
            <strong><?= $this->text('admin-title-email') ?>:</strong>
            <span title="<?= $this->text('admin-rebase-user') ?>" data-url="/api/users/<?= $id ?>/property/email" data-name="value" data-pk="<?= $id ?>" data-type="email" class="<?= $editable_rebase ?>"><?= $this->user->email ?></span>
        </li>
        <li>
            <strong><?= $this->text('admin-title-password') ?>:</strong>
            <span id="password-<?= $id ?>" title="<?= $this->text('admin-password-user') ?>" data-url="/api/users/<?= $id ?>/property/password" data-name="value" data-pk="<?= $id ?>" data-type="password" class="<?= $editable_rebase ?>">******</span>
            <?php if($editable_rebase): ?>
              <button class="editable" data-target="#password-<?= $id ?>" title="<?= $this->text('regular-edit') ?>"><i class="fa fa-edit"></i></button>
            <?php endif ?>
        </li>
        <li>
            <?= $this->insert('dashboard/project/partials/boolean', ['url' => "/api/users/$id/property/hide", 'active' => $this->user->hide, 'label_type' => 'orange']) ?>
            <strong><?= $this->text('admin-user-hidden') ?></strong>
        </li>
        <li>
            <?= $this->insert('dashboard/project/partials/boolean', ['url' => "/api/users/$id/property/active", 'active' => $this->user->active, 'label_type' => 'cyan']) ?>
            <strong><?= $this->text('admin-user-active') ?></strong>
        </li>
    </ul>

    <ul id="role-list" class="admin-list">
        <li>
            <strong><?= $this->text('admin-title-roles') ?>:</strong>
            <button data-toggle="modal" data-target="#admin-modal" data-url="/admin/users/roles?ajax&pronto=true" data-title="<?= $this->text('admin-user-roles') ?>" title="<?= $this->text('admin-show-roles-info') ?>"><i class="fa fa-question"></i></button>
            <?php if($editable_impersonate): ?>
              <button class="<?= $editable_impersonate ?> edit-roles" title="<?= $this->text('admin-edit-roles') ?>" data-target="#roles-<?= $id ?>"><i class="fa fa-edit"></i></button>
            <?php endif ?>
            <br>
            <span id="roles-<?= $id ?>" data-type="checklist" data-value="<?= implode(', ', array_keys($role_names)) ?>" data-source="<?= $this->ee(json_encode($all_roles)) ?>" data-url="/api/users/<?= $id ?>/property/roles" data-name="value" data-pk="<?= $id ?>" data-placement="auto left" class="<?= $editable_impersonate ?>"><?= implode("<br>\n", $role_names) ?></span>

        </li>
    </ul>
  </div>

  <div class="col-sm-6 col-xs-12">
    <ul class="admin-list">
        <?php if($editable_impersonate): ?>
          <li>
            <a href="/admin/users/impersonate/<?= $id ?>" class="text-danger" target="_blank">
                <i class="fa fa-user-md"></i>
                <?= $this->text('admin-impersonate') ?>
            </a>
          </li>
        <?php endif ?>

        <li>
            <a href="/user/profile/<?= $id ?>" target="_blank"><i class="fa fa-external-link"></i> <?= $this->text('dashboard-menu-profile-public') ?></a>
        </li>
        <li>
            <a href="mailto:<?= $this->user->email ?>"><i class="fa fa-at"></i> <?= $this->text('admin-send-email') ?></a>
        </li>
        <li>
            <i class="icon icon-wallet"></i> <?= $this->text('admin-user-wallet-amount') ?>: <strong><?= amount_format($this->user->getPool()->getAmount()) ?></strong>
        </li>
        <li>
            <?php if($projects = $this->user->getProjectNames(5)): ?>
                <strong><?= $this->text('admin-projects') ?>:</strong><br>
                <?php foreach($projects as $pid => $p): ?>
                    <a href="/project/<?= $pid ?>" target="_blank"><?= $p ?></a><br>
                <?php endforeach ?>
                <?php if(count($projects) < $this->user->getTotalProjects()): ?>
                    <a href="/admin/projects?user=<?= $id ?>"><i class="fa fa-external-link"></i> <?= $this->text('admin-view-user-projects') ?></a>
                <?php endif ?>
            <?php else: ?>
                <strong><?= $this->text('admin-user-no-projects') ?></strong>
            <?php endif ?>
        </li>
        <li>
            <?php if($invests = $this->user->getInvests(5)): ?>
                <strong><?= $this->text('admin-invests') ?>:</strong><br>
                <?php foreach($invests as $i): ?>
                    <a href="/admin/accounts/details/<?= $i->id ?>" target="_blank"><?= date_formater($i->invested) ?> <strong><?= amount_format($i->amount) ?></strong></a> <em><?= $i->getProject()->name ?></em><br>
                <?php endforeach ?>
                <?php if(count($invests) < $this->user->getTotalInvests()): ?>
                    <a href="/admin/accounts?name=<?= $this->user->email ?>"><i class="fa fa-external-link"></i> <?= $this->text('admin-view-user-invests') ?></a><br>
                <?php endif ?>
            <?php else: ?>
                <strong><?= $this->text('admin-user-no-invests') ?></strong><br>
            <?php endif ?>
            <a href="/admin/accounts/add?user=<?= $id ?>"><i class="fa fa-plus"></i> <?= $this->text('admin-user-create-invest') ?>:</a>
        </li>
        <li>
            <?php if($mailing = $this->user->getMailing(5)): ?>
                <strong><?= $this->text('admin-mailing') ?>:</strong><br>
                <?php foreach($mailing as $m):
                    $stats = $m->getStats();
                    $opened = (bool) $stats && $stats->getEmailOpenedCollector()->getPercent();
                 ?>
                    <a href="/admin/sent/details/<?= $m->id ?>" target="_blank">
                        <span class="recipient<?= $opened ? ' opened' : '' ?>"></span>
                        <?= date_formater($m->date) ?>
                        <!-- (<strong><?= $m->template ?></strong>) -->
                    </a>
                    <em><?= $m->subject ?></em><br>
                <?php endforeach ?>
                <?php if(count($mailing) < $this->user->getTotalMailing()): ?>
                    <a href="/admin/sent?user=<?= $this->user->email ?>"><i class="fa fa-external-link"></i> <?= $this->text('admin-view-user-mailing') ?></a><br>
                <?php endif ?>
            <?php else: ?>
                <strong><?= $this->text('admin-user-no-mailing') ?></strong><br>
            <?php endif ?>
        </li>
    </ul>


  </div>
</div>
<?php $this->replace() ?>
