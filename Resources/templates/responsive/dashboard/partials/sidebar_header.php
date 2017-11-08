<?php
$user = $this->get_user();
$admin = $this->is_admin();
$truncate = $admin ? 19 : 40;
if(!$user) return;
?>
<div class="sidebar-widget" id="user-<?= $user->id ?>">
    <a class="img-link" href="/dashboard/settings/profile">
        <img class="img-circle" src="<?= $user->avatar->getLink(240, 240, true); ?>">
    </a>
    <div class="content">
        <?php if($admin): ?>
            <span class="label label-danger">ADMIN</span>
        <?php endif ?>
        <div class="title"><a href="/dashboard/settings/profile"><?= $this->text_truncate($user->name, $truncate); ?></a></div>
    </div>
</div>
