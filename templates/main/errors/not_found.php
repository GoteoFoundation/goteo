<?php
//Simple overwrite
$this->layout("$theme::layout", ['title' => 'Error page']);

if($code === 404) {
    $page = $this->page('error');
}
else {
    $page = $this->page('big-error');
}

?>

<?php $this->start('sub-header') ?>
<div>
    <h2><?=$msg?></h2>
    <h3>Error <?=$code?></h3>
</div>
<?php $this->stop() ?>


<div id="main">
    <div class="widget">
        <h3 class="title"><?php echo $page->name; ?></h3>
        <?php echo $page->content; ?>
    </div>
</div>
