<?php
//Simple overwrite
$this->layout("layout", ['title' => 'Access denied']);

$page = $this->page('denied');

?>

<?php $this->section('sub-header') ?>
<div id="sub-header">
    <div>
        <h2><?=$this->raw('msg')?></h2>
        <h3>Error <?=$this->code?></h3>
    </div>
</div>
<?php $this->stop() ?>


<?php $this->section('content') ?>

<div id="main">
    <div class="widget">
        <h3 class="title"><?=$page->name?></h3>
        <?=$page->content?>
    </div>
</div>

<?php $this->stop() ?>
