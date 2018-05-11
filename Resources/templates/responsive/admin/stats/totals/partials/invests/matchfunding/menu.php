<?php
$menu = [];
foreach($this->intervals as $interval => $label) {
    $menu[] = "<a href=\"#$interval\" class=\"btn btn-default". ($interval === $this->interval_active ? ' active' : '') ."\">" . $this->text("admin-$label") . '</a>';
}
?>

<div class="btn-group invests-filter choose-interval" role="group"><?= implode("\n", $menu) ?></div>
