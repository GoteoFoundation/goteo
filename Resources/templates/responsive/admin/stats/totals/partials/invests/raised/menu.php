<?php
$menu1 = $menu2 = [];
foreach($this->methods as $id => $method) {
    $menu1[] = "<li class=\"". ($id === $this->method_active ? ' active' : '') ."\"><a href=\"#$id\">" . $method . '</a></li>';
}
foreach($this->intervals as $interval => $label) {
    $menu2[] = "<a href=\"#$interval\" class=\"btn btn-default". ($interval === $this->interval_active ? ' active' : '') ."\">" . $this->text("admin-$label") . '</a>';
}
?>

<div class="btn-group invests-filter choose-interval" role="group"><?= implode("\n", $menu2) ?></div>
<div class="btn-group pull-right">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?= $this->text('invest-payment-method') ?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu invests-filter choose-method">
    <?= implode("\n", $menu1) ?>
  </ul>
</div>
