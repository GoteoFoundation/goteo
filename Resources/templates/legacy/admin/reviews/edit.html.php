<?php

use Goteo\Library\Text;

$project = $vars['project'];
$review  = $vars['review'];
?>
<div class="widget board">
    <p>
        Comentario de <?php echo $project->user->name; ?>:<br />
        <?php echo $project->comment; ?>
    </p>

    <form method="post" action="/admin/reviews/<?php echo $vars['action']; ?>/<?php echo $project->id; ?>">

        <input type="hidden" name="id" value="<?php echo $review->id; ?>" />
        <input type="hidden" name="project" value="<?php echo $project->id; ?>" />

        <p>
            <label for="review-to_checker">Comentario para el revisor:</label><br />
            <textarea name="to_checker" id="review-to_checker" cols="60" rows="10"><?php echo $review->to_checker; ?></textarea>
        </p>

        <p>
            <label for="review-to_owner">Comentario para el productor:</label><br />
            <textarea name="to_owner" id="review-to_owner" cols="60" rows="10"><?php echo $review->to_owner; ?></textarea>
        </p>

       <input type="submit" name="save" value="Guardar" />
    </form>
</div>
