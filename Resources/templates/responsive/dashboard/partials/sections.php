<nav class="dashboard-sections">
  <div class="inner-container">
      <ul class="nav nav-tabs nav-justified">
        <li<?= ($this->section == "activity") ? ' class="active"' : '' ?>>
            <a href="/dashboard/activity" title="<?= $this->text('dashboard-menu-activity') ?>">
                <i class="icon icon-activity icon-5x"></i>
                <br>
                <?= $this->text('dashboard-menu-activity') ?>
            </a>
        </li>
        <li<?= ($this->section == "projects") ? ' class="active"' : '' ?>>
            <a href="/dashboard/projects" title="<?= $this->text('dashboard-menu-projects') ?>">
                <i class="icon icon-projects icon-5x"></i>
                <br>
                <?= $this->text('dashboard-menu-projects') ?>
            </a>
        </li>
        <li<?= ($this->section == "wallet") ? ' class="active"' : '' ?>>
            <a href="/dashboard/wallet" title="<?= $this->text('dashboard-menu-pool') ?>">
                <i class="icon icon-wallet icon-5x"></i>
                <br>
                <?= $this->text('dashboard-menu-pool') ?>
            </a>
        </li>
        <li<?= ($this->section == "settings") ? ' class="active"' : '' ?>>
            <a href="/dashboard/settings" title="<?= $this->text('dashboard-menu-profile-preferences') ?>">
                <i class="icon icon-settings icon-5x"></i>
                <br>
                <?= $this->text('dashboard-menu-profile-preferences') ?>
            </a>
        </li>
      </ul>
  </div>
</nav>
