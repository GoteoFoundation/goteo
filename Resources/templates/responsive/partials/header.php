<nav class="navbar navbar-default top-navbar">
  <div class="container">
    <div class="navbar-header">
      <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= SRC_URL ?>/assets/img/logo.svg" class="img-responsive logo" alt="Goteo"></a>
    </div><!--/.navbar-header -->

    <div class="navbar-always">
      <?php if (!$this->get_user()) : ?>
        <a class="hidden-xs" href="/signup?return=<?= $this->get_uri() ?>"><?= $this->text('menu-signup') ?></a>
        <a title="<?= $this->text('menu-login') ?>" href="/login?return=<?= $this->get_uri() ?>"><i class="fa fa-user-circle"></i><span class="hidden-xs"> <?= $this->text('menu-login') ?></span>
      <?php else: ?>

        <button class="toggle-menu user-menu" data-target="user-menu" title="Personal options"><img class="img-circle" src="<?= $this->get_user()->avatar->getLink(64, 64, true); ?>"></button>
      <?php endif ?>
      <button class="toggle-menu main-menu" data-target="main-menu" title="Main menu"><i class="fa fa-fw fa-bars show-menu"></i><i class="fa fa-fw fa-close hide-menu"></i></button>
    </div>

    <div class="sidebar-menu" id="user-menu">
      <?= $this->supply('global-user-menu', $this->insert('partials/header/menu', ['target' => 'user-menu', 'menu' => $this->get_user_menu()])) ?>
    </div>

    <div class="sidebar-menu" id="main-menu">
      <?= $this->supply('global-main-menu', $this->insert('partials/header/menu', ['target' => 'main-menu', 'menu' => $this->get_main_menu(), 'bottom' => [['link' => '/project/create', 'class' => 'btn-fashion', 'text' => $this->text('regular-create')] ]])) ?>
    </div>

  </div><!--/.container -->
</nav>
