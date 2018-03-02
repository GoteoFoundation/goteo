<?php
$user = $this->get_user();
$admin = $this->is_admin();
if(!$user) return;
?>
<div class="sidebar-widget" id="user-<?= $user->id ?>">
    <a class="img-link" href="/admin/<?= $this->module_id ?>">
        <?php if($this->icon): ?>
                <?= $this->raw('icon') ?>
        <?php else: ?>
                <img class="img-circle" src="<?= $user->avatar->getLink(240, 240, true); ?>">
        <?php endif ?>
    </a>
    <div class="content">
        <?php if($admin): ?>
            <span class="label label-danger">ADMIN</span>
        <?php endif ?>
        <div class="title"><a href="/dashboard/settings/profile"><?= $this->text_truncate($user->name, 40); ?></a></div>
    </div>
</div>
