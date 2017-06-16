<?php if($this->menu || $this->bottom): ?>
  <nav id="sidebar-menu">
    <button class="toggle-sidebar btn btn-link visible-xs" title="Close"><i class="fa fa-close"></i></button>

    <div class="sidebar-header">
      <a href="<?= SITE_URL ?>"><img src="<?= SRC_URL ?>/goteo_logo.png" class="logo" alt="Goteo"></a>
    </div>

    <ul class="nav">
      <?php foreach($this->raw('menu') as $link => $item): ?>
        <?= $this->insert('partials/header/menu_item', ['link' => $link, 'item' => $item, 'active' => $this->zone]) ?>
      <?php endforeach ?>
    </ul>

  <?php if($this->bottom): ?>
    <ul class="nav bottom">
      <?php foreach($this->raw('bottom') as $link => $item): ?>
        <?= $this->insert('partials/header/menu_item', ['link' => $link, 'item' => $item, 'active' => $this->zone]) ?>
      <?php endforeach ?>
    </ul>
  <?php endif ?>

  </nav>

  <div id="sidebar-menu-toggle" class="toggle-sidebar visible-xs" title="Open menu">
    <i class="fa fa-angle-double-right"></i>
  </div>
<?php endif ?>
