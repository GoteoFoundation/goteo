<?php
$percent = intval($this->percent);
$style = $this->style ? $this->style : 'cyan';
?>
<div class="c100 p<?= $percent ?> <?= $style ?>">
    <span class="percent"><?= $percent ?>%</span>
    <div class="slice">
        <div class="bar"></div>
        <div class="fill"></div>
    </div>
</div>
