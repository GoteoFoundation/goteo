<ul class="nav nav-tabs">
<?php foreach($this->tabs as $id => $parts): ?>
  <li role="presentation" <?= $id === $this->tab ? 'class="active"' : '' ?>><a href="/admin/categories/<?= $id ?>"><?= $this->text("regular-" . $parts['text']) ?></a></li>
<?php endforeach ?>
</ul>
