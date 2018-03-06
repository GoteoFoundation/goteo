<?php

$this->layout("translate/layout");


$this->section('translate-content');

$fields = $this->fields;
$q = $this->get_query('q');
$query = $this->get_query() ? '?'. http_build_query($this->get_query()) : '';


?>
<div class="dashboard-content">
  <div class="inner-container">

    <h3><?= $this->text('regular-recent-activity') ?></h3>

    <ul class="list-group">
        <?php foreach ($this->feed as $item): ?>
            <li class="list-group-item">
                <span class="badge"><?= $this->text('feed-timeago', $item->timeago) ?></span>
                <?php echo $item->html; ?>
            </li>
        <?php endforeach ?>
    </ul>
  </div>
</div>

<?php $this->replace() ?>
