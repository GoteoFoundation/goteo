<?php
$menu1 = $menu2 = [];
foreach($this->methods as $id => $method) {
    $menu1[] = "<a href=\"#$id\" class=\"btn btn-default". ($id === $method_active ? ' active' : '') ."\">" . $method . '</a>';
}
foreach($this->intervals as $interval => $label) {
    $menu2[] = "<a href=\"#$interval\" class=\"btn btn-default". ($interval === $interval_active ? ' active' : '') ."\">" . $this->text("admin-$label") . '</a>';
}
?>

<div class="btn-group choose-method" role="group"><?= implode("\n", $menu1) ?></div>
<div class="btn-group choose-interval" role="group"><?= implode("\n", $menu2) ?></div>
