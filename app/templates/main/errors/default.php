<?php
$this->layout("$theme::layout", ['title' => 'Unexpected Error']);

$this->start('sub-header') ?>

<div id="sub-header">
    <div>
        <h2><?=$msg?></h2>
        <h3>Error <?=$code?></h3>
    </div>
</div>
<?php $this->stop() ?>
