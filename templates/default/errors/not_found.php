<?php
//Simple overwrite
$this->layout("layout", ['title' => 'Not found error']);

if($this->code === 404) {
    $page = $this->page('error');
}
else {
    $page = $this->page('big-error');
}

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
