<div class="menu-header">
    <a class="navbar-brand" href="<?= $this->get_config('url.main') ?>"><img src="<?= $this->asset('img/goteo.svg') ?>" class="img-responsive logo" alt="Goteo"></a>
    <button class="toggle-menu btn btn-link" data-target="<?= $this->target ?>" title="Close"><i class="icon icon-close"></i></button>
</div>

<ul class="nav">
  <?php foreach($this->raw('menu') as $link => $item): ?>
    <?= $this->insert('partials/header/menu_item', ['link' => $link, 'item' => $item]) ?>
  <?php endforeach ?>
</ul>

<?php if($this->bottom): ?>
<ul class="nav bottom">
  <?php foreach($this->raw('bottom') as $link => $item): ?>
    <?= $this->insert('partials/header/menu_item', ['link' => $link, 'item' => $item]) ?>
  <?php endforeach ?>
</ul>
<?php endif ?>
