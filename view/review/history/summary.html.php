<?php
use Goteo\Core\View,
    Goteo\Model;

?>
<div class="widget projects">
    <h2 class="title">Mis revisiones</h2>
    <?php foreach ($this['reviews'] as $reviews) : ?>
        <div>
            <?php
            \trace($review);
            ?>
        </div>
    <?php endforeach; ?>
</div>