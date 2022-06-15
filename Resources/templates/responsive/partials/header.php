<nav class="navbar navbar-default top-navbar <?= $this->navClass ? $this->navClass  : '' ?>  <?= ($this->premium)? "premium" : "" ?>" <?php if($this->background) echo ' style="background-color:'.$this->background.'"'; ?>>
  <div class="container-fluid">
    <div class="navbar-header">
      <?php $this->section('header-navbar-brand') ?>
      <?php if($this->navLogo=='black'): ?>
      <a class="navbar-brand" href="<?= $this->get_config('url.main') ?>"><img src="<?= $this->asset('img/goteo.svg') ?>" class="logo" alt="Goteo"></a>
      <?php elseif($this->premium): ?>
      <a class="navbar-brand" href="<?= $this->get_config('url.main') ?>"><img src="<?= $this->asset('img/logo-fg-white.png') ?>" class="logo premium" alt="Goteo"></a>
      <?php else: ?>
      <a class="navbar-brand" href="<?= $this->get_config('url.main') ?>"><img src="<?= $this->asset('img/goteo-white.svg') ?>" class="logo" alt="Goteo"></a>
      <?php endif ?>
      <?php $this->stop(); ?>
    </div><!--/.navbar-header -->

    <?php $this->section('header-navbar-powered') ?>
    <?php $this->stop(); ?>

    <div class="navbar-always">
      <?php if (!$this->get_user()) : ?>
        <a class="hidden-xs" href="/signup?return=<?= $this->get_uri() ?>"><?= $this->text('menu-signup') ?></a>
        <a title="<?= $this->text('menu-login') ?>" class="user-menu" href="/login?return=<?= $this->get_uri() ?>"><i class="icon icon-user"></i><span class="hidden-xs"> <?= $this->text('menu-login') ?></span></a>
      <?php else: ?>
        <button class="toggle-menu user-menu" data-target="user-menu" title="<?= $this->text('regular-menu-personal') ?>">
            <img class="img-circle" src="<?= $this->get_user()->avatar->getLink(64, 64, true); ?>" alt="<?= $this->get_user()->name ?>">
        </button>
      <?php endif ?>

      <button class="toggle-menu main-menu" data-target="main-menu" title="Main menu">
        <span class="show-menu"><i class="fa fa-bars"></i></span>
        <span class="close-menu"><i class="icon icon-close"></i></span>
      </button>

      <?php if($this->powered): ?>
      <div class="powered hidden-xs">
      <span><?= $this->text('call-header-powered-by') ?></span>
        <a href="/">
          <img height="20" src="<?= '/assets/img/goteo-white.svg' ?>" >
        </a>
      </div>
      <?php endif ?>

    </div>

    <div class="top-menu" id="user-menu">
      <?= $this->supply('global-user-menu', $this->insert('partials/header/menu', ['target' => 'user-menu', 'menu' => $this->get_user_menu()])) ?>
    </div>

    <div class="top-menu" id="main-menu">
      <?= $this->supply('global-main-menu', $this->insert('partials/header/menu', ['target' => 'main-menu', 'menu' => $this->get_main_menu(), 'bottom' => [['link' => $this->lang_host() . 'project/create', 'a_class' => 'btn-fashion', 'text' => $this->text('regular-create')] ]])) ?>
    </div>

  </div><!--/.container -->
</nav>
