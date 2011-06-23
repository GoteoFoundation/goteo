<?php
use Goteo\Core\View,
    Goteo\Model;

?>
<?php if (!empty($this['reviews'])) : ?>
    <h2 class="title">Mis revisiones anteriores</h2>
    <?php foreach ($this['reviews'] as $review) : ?>
        <div class="widget">
            <p>El proyecto <strong><?php echo $review->name; ?></strong> de <strong><?php echo $review->owner_name; ?></strong></p>
            <p>La edición del proyecto alcanzó el <strong><?php echo $review->progress; ?>%</strong>, la puntuación de la revisión fue de <strong><?php echo $review->score; ?>/<?php echo $review->max; ?></strong></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>