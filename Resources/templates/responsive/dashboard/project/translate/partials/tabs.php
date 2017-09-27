<?php
$zones = $this->a('zones');
if(!$zones) return;
?>
<ul class="nav nav-tabs nav-justified">
<?php
    foreach($zones as $zone => $name):
?>
  <li role="tab" <?= $this->step == $zone ? ' class="active"' : '' ?>><a href="/dashboard/project/<?= $this->project->id ?>/translate/<?= $zone ?>/<?= $this->lang ?>"><?= $name ?></a></li>
<?php endforeach ?>

</ul>
