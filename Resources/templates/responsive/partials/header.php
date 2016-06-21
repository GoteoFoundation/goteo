<?php

use Goteo\Library\Currency;

$langs = $this->lang_list('name');
$currencies = Currency::$currencies;
$select_currency=Currency::$currencies[$_SESSION['currency']]['html'];

?>
    <!-- Static navbar -->
      <nav class="navbar navbar-default main-color-background">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= SRC_URL ?>/assets/img/logo.svg" class="img-responsive logo" alt="Goteo"></a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li><a href="/faq"><?=$this->text('regular-header-faq') ?></a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $select_currency." ".$_SESSION['currency'] ?> <span class="caret"></span></a>
                <ul class="dropdown-menu language-dropbox">
                  <?php foreach ($currencies as $ccyId => $ccy): ?>
                  <?php if ($ccyId == $_SESSION['currency']) continue; ?>
                    <li>
                      <a href="?currency=<?= $ccyId ?>"><?= $ccy['html'].' '.$ccyId ?></a>
                    </li>
                  <?php endforeach ?>
                </ul>
              </li>

              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= strtoupper($this->lang_current()) ?> <span class="caret"></span></a>
                <ul class="dropdown-menu language-dropbox">
                  <?php foreach ($langs as $id => $lang): ?>
                  <?php if ($this->lang_active($id)) continue; ?>
                    <li>
                      <a href="<?= $this->lang_url($id) ?>"><?= $lang ?></a>
                    </li>
                  <?php endforeach ?>
                </ul>
              </li>

              <?php if (!$this->get_user()) : ?>
              <li><a href="/signup?return=<?= $this->get_uri() ?>"><?= $this->text('menu-signup') ?></a></li>
              <li><a href="/login?return=<?= $this->get_uri() ?>"><?= $this->text('menu-login') ?></a></li>
              <?php else: ?>
               <li class="dropdown">
                <a href="#" class="dropdown-toggle avatar-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" ><span></span><img class="avatar-radius" src="<?= $this->get_user()->avatar->getLink(35, 35, true); ?>" /><span class="caret avatar-arrow"></span></a>
                <ul class="dropdown-menu language-dropbox">
                  <li><a href="/dashboard"><?= $this->text('dashboard-menu-main') ?></a></li>
                  <li><a href="/dashboard/profile"><?= $this->text('dashboard-menu-profile') ?></a></li>
                  <li><a href="/dashboard/wallet"><?= $this->text('dashboard-menu-pool') ?></a></li>
                  <li><a href="/dashboard/activity"><?= $this->text('dashboard-menu-activity') ?></a></li>
                  <li><a href="/dashboard/projects"><?= $this->text('dashboard-menu-projects') ?></a></li>
                  <li><a href="/dashboard/profile/preferences"><?= $this->text('dashboard-menu-profile-preferences'); ?></a></li>
                  <?php if ( isset($this->get_user()->roles['caller']) ) : ?>
                      <li><a href="/dashboard/calls"><?= $this->text('dashboard-menu-calls') ?></a></li>
                  <?php endif; ?>

                  <?php if ( isset($this->get_user()->roles['translator']) ||  isset($this->get_user()->roles['admin']) || isset($this->get_user()->roles['superadmin']) ) : ?>
                       <li><a href="/translate"><?= $this->text('regular-translate_board') ?></a></li>
                   <?php endif; ?>

                   <?php if ( isset($this->get_user()->roles['checker']) ) : ?>
                      <li><a href="/review"><?= $this->text('regular-review_board') ?></a></li>
                  <?php endif; ?>

                  <?php if ( $this->is_admin() ): ?>
                      <li><a href="/admin"><?= $this->text('regular-admin_board') ?></a></li>
                  <?php endif; ?>

                   <li role="separator" class="divider"></li>
                   <li class="logout"><a href="/logout?return=<?= $this->get_uri() ?>"><?= $this->text('regular-logout') ?></a></li>
                </ul>
              </li>
              <?php endif; ?>

            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
