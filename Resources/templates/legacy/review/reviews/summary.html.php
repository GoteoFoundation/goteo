<?php
use Goteo\Core\View;

echo View::get('review/reviews/selector.html.php', $vars);

$review = $vars['review'];

?>

<div class="widget">
    <p>El proyecto <strong><?php echo $review->name; ?></strong> de <strong><?php echo $review->owner_name; ?></strong></p>
    <p>La edición del proyecto alcanzó el <strong><?php echo $review->progress; ?>%</strong>, la puntuación actual de la revisión es de <strong><?php echo $review->score; ?>/<?php echo $review->max; ?></strong></p>
    <p><a href="<?php echo '/project/' . $review->project; ?>" target="_blank">Ver el proyecto</a>
        <?php if ($review->project_status < 3) : ?><br /><a href="<?php echo '/project/edit/' . $review->project; ?>" target="_blank">Abrir la edición del proyecto</a><?php endif; ?>
        <br /><a href="<?php echo '/user/' . $review->owner; ?>" target="_blank">Abrir el perfil del creador</a>
    </p>
</div>

<div class="widget">
    Comentario del administrador:<br />
    <blockquote><?php echo $review->comment; ?></blockquote>
</div>

<div class="widget">
    <p>Tu revisión está <?php echo $review->ready == 1 ? 'Lista' : 'Pendiente'; ?><?php if ($review->ready != 1) : ?> Puedes completarla en <a href="/review/reviews/evaluate/open/<?php echo $review->id; ?>">tus revisiones</a><?php endif; ?></p>
    <?php
    if ($review->ready == 0) {
        echo '<a href="/review/reviews/summary/ready/' . $review->id . '">[Dar por terminada mi revisión]</a>';
    } else {
        echo 'Continúa con otra o espera instrucciones.';
    }
    ?>
</div>
