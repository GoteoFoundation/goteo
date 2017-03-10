<nav class="dashboard-navbar-nav">
      <div class="container">
      	<div class="user-info row no-margin">
      		<img class="avatar-radius pull-left" src="<?= $this->get_user()->avatar->getLink(56, 56, true); ?>" />
      		<div class="pull-left panel-title">
      		<?= $this->text('dashboard-menu-main') ?>
			</span>
      	</div>
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Menu</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div id="navbar" class="clear-both">
          <ul class="nav navbar-nav dashboard-navbar">

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $this->text('dashboard-menu-activity') ?> <span class="caret"></a>
              <ul class="dropdown-menu">
                <?php $this->section('dashboard-menu-item-activity') ?>
                <li><a href="/dashboard/activity/summary"><?= $this->text('dashboard-menu-activity-summary') ?></a></li>
                <li><a href="/dashboard/activity/apikey"><?= $this->text('dashboard-menu-activity-apikey') ?></a></li>
                <?php $this->stop() ?>
              </ul>
            </li>

            <li class="dropdown <?= ($this->section=="pool") ? 'active' : '' ?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $this->text('dashboard-menu-pool') ?> <span class="caret"></a>
              <ul class="dropdown-menu">
                <?php $this->section('dashboard-menu-item-pool') ?>
                <li><a href="/dashboard/wallet"><?= $this->text('dashboard-menu-pool-available') ?></a></li>
                <!--<li><a href="/dashboard/wallet/conditions"><?= $this->text('dashboard-menu-pool-conditions') ?></a></li>-->
                <?php $this->stop() ?>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $this->text('dashboard-menu-profile') ?> <span class="caret"></a>
              <ul class="dropdown-menu">
                <?php $this->section('dashboard-menu-item-profile') ?>
                <li><a href="/dashboard/profile/profile"><?= $this->text('dashboard-menu-profile-profile') ?></a></li>
                <li><a href="/dashboard/profile/personal"><?= $this->text('dashboard-menu-profile-personal') ?></a></li>
                <li><a href="/dashboard/profile/location"><?= $this->text('dashboard-menu-profile-location') ?></a></li>
                <li><a href="/dashboard/profile/access"><?= $this->text('dashboard-menu-profile-access') ?></a></li>
                <li><a href="/dashboard/profile/preferences"><?= $this->text('dashboard-menu-profile-preferences') ?></a></li>
                <li><a href="/dashboard/profile/public"><?= $this->text('dashboard-menu-profile-public') ?></a></li>
                <?php $this->stop() ?>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $this->text('dashboard-menu-projects') ?> <span class="caret"></a>
              <ul class="dropdown-menu">
                <?php $this->section('dashboard-menu-item-projects') ?>
                <li><a href="/dashboard/projects/summary"><?= $this->text('dashboard-menu-projects-summary') ?></a></li>
                <li><a href="/dashboard/projects/updates"><?= $this->text('dashboard-menu-projects-updates') ?></a></li>
                <li><a href="/dashboard/projects/supports"><?= $this->text('dashboard-menu-projects-supports') ?></a></li>
                <li><a href="/dashboard/projects/rewards"><?= $this->text('dashboard-menu-projects-rewards') ?></a></li>
                <li><a href="/dashboard/projects/messengers"><?= $this->text('dashboard-menu-projects-messegers') ?></a></li>
                <li><a href="/dashboard/projects/contract"><?= $this->text('dashboard-menu-projects-contract') ?></a></li>
                <li><a href="/dashboard/projects/analytics"><?= $this->text('dashboard-menu-projects-analytics') ?></a></li>
                <li><a href="/dashboard/projects/shared-materials"><?= $this->text('dashboard-menu-projects-shared-materials') ?></a></li>
                <?php $this->stop() ?>
              </ul>
            </li>

          </ul>
        </div>
      </div>
    </nav>
