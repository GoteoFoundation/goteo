<ul class="nav nav-tabs">
<?php foreach($this->tabs as $id => $tab): ?>
  <li role="presentation" <?= $id === $this->tab ? 'class="active"' : '' ?>><a href="/admin/categories/<?= $id ?>"><?= $this->text("regular-$tab") ?></a></li>
<?php endforeach ?>
</ul>
