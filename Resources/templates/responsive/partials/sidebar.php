<?php if($this->sidebarMenu || $this->sidebarBottom): ?>
  <nav id="sidebar-menu" class="<?= $this->sidebarClass ?>">
    <div class="sidebar-wrap">
        <button class="toggle-sidebar btn btn-link" title="Close"><i class="fa fa-close"></i></button>

        <div class="sidebar-header">
          <?= $this->supply('sidebar-header') ?>
        </div>

        <ul class="nav sidebar-nav">
          <?php foreach($this->raw('sidebarMenu') as $link => $item): ?>
            <?= $this->insert('partials/header/menu_item', ['link' => $link, 'item' => $item, 'active' => $this->zone]) ?>
          <?php endforeach ?>
        </ul>

      <?php if($this->sidebarBottom): ?>
        <ul class="nav sidebar-nav bottom">
          <?php foreach($this->raw('sidebarBottom') as $link => $item): ?>
            <?= $this->insert('partials/header/menu_item', ['link' => $link, 'item' => $item, 'active' => $this->zone]) ?>
          <?php endforeach ?>
        </ul>
      <?php endif ?>

      <div class="sidebar-footer">
        <?= $this->supply('sidebar-footer') ?>
      </div>
    </div>
  </nav>

  <div id="sidebar-menu-toggle" class="toggle-sidebar visible-xs" title="Open menu">
      <?= $this->supply('sidebar-menu-toggle', '<i class="fa fa-angle-double-right"></i><i class="fa fa-angle-double-left"></i>') ?>
  </div>
<?php endif ?>
