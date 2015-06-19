<?php
use Goteo\Core\View,
    Goteo\Model;

?>
<?php if (!empty($vars['reviews'])) : ?>
    <h2 class="title">Mis revisiones actuales</h2>
    <?php foreach ($vars['reviews'] as $review) : ?>
        <div class="widget">
            <p>El proyecto <strong><?php echo $review->name; ?></strong> de <strong><?php echo $review->owner_name; ?></strong></p>
            <p>La edición del proyecto alcanzó el <strong><?php echo $review->progress; ?>%</strong>, la puntuación actual de la revisión es de <strong><?php echo $review->score; ?>/<?php echo $review->max; ?></strong></p>
            <p>Tu revisión está <?php echo $review->ready == 1 ? 'Lista' : 'Pendiente'; ?><?php if ($review->ready != 1) : ?> Puedes completarla en <a href="/review/reviews/evaluate/open/<?php echo $review->id; ?>">tus revisiones</a><?php endif; ?></p>
            <p><a href="<?php echo '/project/' . $review->project; ?>" target="_blank">Ver el proyecto</a>
                <?php if ($review->project_status < 3) : ?><br /><a href="<?php echo '/project/edit/' . $review->project; ?>" target="_blank">Abrir la edición del proyecto</a><?php endif; ?>
                <br /><a href="<?php echo '/user/' . $review->owner; ?>" target="_blank">Abrir el perfil del creador</a>
            </p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
