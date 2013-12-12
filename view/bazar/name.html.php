<?php
$item = $this['item'];
?>
<section>
  <?php if (!empty($item->img)) : ?>
    <img src="<?php echo $item->imgsrc; ?>" title="<?php echo $item->title; ?>" alt="IMG"/>
  <?php endif; ?>
  <div id="caja"><p><?php echo $item->amount; ?>&euro;</p></div>

  <p><?php echo $item->title; ?> </p>
</section>
