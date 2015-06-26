<?php
$type = $this->type ? $this->type : 'available';

?>
<div class="side_widget">
    <div class="block categories rounded-corners">
        <p class="title"><?= $this->text('node-side-searcher-bycategory') ?></p>
        <ul class="menu">
            <?php foreach ($this->categories as $cat => $name) : ?>
            <li<?= $cat == $this->category ? ' class="selected"' : '' ?>><a href="<?= '/channel/' . $this->channel->id . '/' . $type .'/' . $cat ?>" class="show_cat"><?= $name ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>
