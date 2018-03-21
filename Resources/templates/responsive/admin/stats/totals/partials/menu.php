<ul class="nav nav-pills">
  <?php foreach(['project', 'invest'] as $k): 
    $plural = $k . 's';
  ?>
    <li role="presentation" <?= $plural === $this->part ? 'class="active"' : '' ?>><a href="/admin/stats/totals/<?= $plural ?>"><?= $this->text("admin-stats-$k-totals") ?></a></li>
  <?php endforeach ?>
</ul>