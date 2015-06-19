<?php

$categories  = $this->categories;

?>
<div class="side_widget">
    <div class="block categories rounded-corners">
        <p class="title"><?= $this->text('node-side-searcher-bycategory') ?></p>
        <ul>
            <?php foreach ($categories as $cat=>$catData) : ?>
            <li><a href="<?= $URL.'/channel/'.$this->channel->id.'/category/'.$cat.'#content' ?>" class="show_cat"><?= $catData ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>
