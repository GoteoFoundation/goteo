<?php

$langs = $this->lang_list('name');

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
            <a class="navbar-brand" href="#"><img src="<?= SRC_URL ?>/assets/img/logo.png" class="img-responsive logo" alt="Goteo"></a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <?php if (!$this->get_user()) : ?>
              <li><a href="/signup"><?= $this->text('menu-signup') ?></a></li>
              <li><a href="/login"><?= $this->text('menu-login') ?></a></li>
              <?php else: ?>
              <li>
              <a href="/dashboard" class="avatar-link"><span></span><img class="avatar-radius" src="<?= $this->get_user()->avatar->getLink(35, 35, true); ?>" /></a>
              </li>
              <?php endif; ?>
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
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
