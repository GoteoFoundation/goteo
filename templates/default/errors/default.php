<?php
$this->layout("layout", ['title' => 'Unexpected Error']);

$this->section('sub-header')
?>

<div id="sub-header">
    <div>
        <h2><?=$this->msg?></h2>
        <h3>Error <?=$this->code?></h3>
    </div>
</div>

<?php $this->replace() ?>
