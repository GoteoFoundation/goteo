<nav class="navbar navbar-default top-navbar">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= $this->asset('img/goteo-white.svg') ?>" class="logo" alt="Goteo"></a>
    </div><!--/.navbar-header -->

    <div class="navbar-always">
      <?php if (!$this->get_user()) : ?>
        <a class="hidden-xs" href="/signup?return=<?= $this->get_uri() ?>"><?= $this->text('menu-signup') ?></a>
        <a title="<?= $this->text('menu-login') ?>" class="user-menu" href="/login?return=<?= $this->get_uri() ?>"><i class="icon icon-user"></i><span class="hidden-xs"> <?= $this->text('menu-login') ?></span></a>
      <?php else: ?>
        <button class="toggle-menu user-menu" data-target="user-menu" title="<?= $this->text('general-menu-personal') ?>"><img class="img-circle" src="<?= $this->get_user()->avatar->getLink(64, 64, true); ?>"></button>
      <?php endif ?>

      <button class="toggle-menu main-menu" data-target="main-menu" title="Main menu">
        <span class="show-menu"><i class="fa fa-bars"></i></span>
        <span class="close-menu"><i class="icon icon-close"></i></span>
      </button>
    </div>

    <div class="top-menu" id="user-menu">
      <?= $this->supply('global-user-menu', $this->insert('partials/header/menu', ['target' => 'user-menu', 'menu' => $this->get_user_menu()])) ?>
    </div>

    <div class="top-menu" id="main-menu">
      <?= $this->supply('global-main-menu', $this->insert('partials/header/menu', ['target' => 'main-menu', 'menu' => $this->get_main_menu(), 'bottom' => [['link' => '/project/create', 'a_class' => 'btn-fashion', 'text' => $this->text('regular-create')] ]])) ?>
    </div>

  </div><!--/.container -->
</nav>
