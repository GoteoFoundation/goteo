<?php
$item = $this['item'];
?>
<section>
	<img src="<?php echo $item->imgsrc; ?>" title="<?php echo $item->title; ?>" alt="IMG" title="<?php echo $item->title; ?>"/>
	<div id="caja"><p class="precio"><?php echo $item->amount; ?>&euro;</p></div>
	<p class="desc"><?php echo $item->title; ?></p>
</section>
