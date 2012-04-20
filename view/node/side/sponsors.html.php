<?php
use Goteo\Library\Text,
    Goteo\Core\View;
?>
<div class="side_widget sponsors">
    <p class="title">
        <span class="line"></span>
        Con el apoyo de:
    </p>
    <!-- logos de los patrocinadores --->
    <?php foreach ($this['sponsors'] as $sponsor) : ?>
    <div class="logo">
        <a href="<?php echo $sponsor->url ?>" title="<?php echo $sponsor->name ?>" target="_blank" rel="nofollow"><img src="<?php echo $sponsor->image->getLink(150, 85) ?>" alt="<?php echo $sponsor->name ?>" /></a>
    </div>
    <?php endforeach; ?>
</div>