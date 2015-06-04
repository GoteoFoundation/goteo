<div class="side_widget">
    <?php foreach ($this->searcher as $cat => $label) : ?>
    <div class="line button rounded-corners<?php if ($cat == 'promote' && !$this->hide_promotes) echo ' current' ?>">
        <p><a href="<?php echo $cat ?>" class="show_cat"><?php echo $label ?></a></p>
    </div>
    <?php endforeach; ?>

</div>
