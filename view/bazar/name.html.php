<?php
$item = $this['item'];
$item->imgsrc = (!empty($item->img)) ? '/data/images/'.$item->img->name : '/data/images/bazaritem.svg';
?>
<article class="header">
	<div class="caja"><p class="precio"><?php echo $item->amount; ?>&euro;</p></div>
	<img src="<?php echo $item->imgsrc; ?>" title="<?php echo $item->title; ?>" alt="IMG" title="<?php echo $item->title; ?>"/>
	<p class="desc"><?php echo $item->description; ?></p>
</article>
