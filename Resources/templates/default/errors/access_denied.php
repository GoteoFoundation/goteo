<?php
//Simple overwrite
$this->layout("layout", ['title' => 'Access denied']);

$page = $this->page('denied');

?>

<?php $this->section('sub-header') ?>
<div id="sub-header">
    <div>
        <h2><?= $this->raw('title') ?></h2>
        <h3>Error <?= $this->code ?></h3>
        <p><?= $this->raw('msg') ?></p>
    </div>
</div>
<?php $this->stop() ?>


<?php $this->section('content') ?>

<div id="main">
    <div class="widget">
        <h3 class="title"><?=$page->name?></h3>
        <?= $page->parseContent() ?>
    </div>
</div>

<?php $this->stop() ?>


