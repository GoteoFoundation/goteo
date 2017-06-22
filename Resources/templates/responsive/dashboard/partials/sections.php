<nav class="dashboard-sections">
  <ul class="nav nav-tabs nav-justified">
    <li<?= ($this->section == "activity") ? ' class="active"' : '' ?>><a href="/dashboard/activity" title="<?= $this->text('dashboard-menu-activity') ?>"><i class="icon icon-activity icon-6x"></i></a></li>
    <li<?= ($this->section == "projects") ? ' class="active"' : '' ?>><a href="/dashboard/projects" title="<?= $this->text('dashboard-menu-projects') ?>"><i class="icon icon-projects icon-6x"></i></a></li>
    <li<?= ($this->section == "wallet") ? ' class="active"' : '' ?>><a href="/dashboard/wallet" title="<?= $this->text('dashboard-menu-pool') ?>"><i class="icon icon-wallet icon-6x"></i></a></li>
    <li<?= ($this->section == "settings") ? ' class="active"' : '' ?>><a href="/dashboard/settings" title="<?= $this->text('dashboard-menu-profile-preferences') ?>"><i class="icon icon-settings icon-6x"></i></a></li>
  </ul>
</nav>
