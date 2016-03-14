<?php
$this->layout("layout", ['title' => 'Unexpected Error']);

$this->section('sub-header')
?>

<div id="sub-header">
    <div>
        <h2><?= $this->raw('title') ?></h2>
        <h3>Error <?= $this->code ?></h3>
        <p><?= $this->raw('msg') ?></p>
    </div>
</div>

<?php $this->replace() ?>
