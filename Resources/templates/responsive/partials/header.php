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
      <ul class="nav">
        <li><a href="#">Item 1</a></li>
        <li><a href="#">Item 2</a></li>
      </ul>
    </div>

    <div class="sidebar-menu" id="main-menu">
      <div class="sidebar-header">
        <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= SRC_URL ?>/goteo_logo.png" class="img-responsive logo" alt="Goteo"></a>
        <button class="toggle-menu btn btn-link" data-target="main-menu" title="Close"><i class="fa fa-close"></i></button>
      </div>
      <ul class="nav">
        <li><a href="#">MainItem 1</a></li>
        <li><a href="#">MainItem 2</a></li>
      </ul>
    </div>

  </div><!--/.container -->
</nav>
