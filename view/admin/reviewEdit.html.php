<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

$project = $this['project'];
$review  = $this['review'];

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Revisión de proyectos</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li><a href="/admin/checking/?filter=<?php echo $this['filter']; ?>">Revisiones</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h3>Iniciando nueva revisión para el proyecto '<?php echo $project->name; ?>'</h3>
                    <?php break;
                case 'edit': ?>
                    <h3>Editando la revisión para el proyecto '<?php echo $project->name; ?>'</h3>
                    <?php break;
            } ?>

            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <div class="widget board">
                <p>
                    Comentario de <?php echo $project->user->name; ?>:<br />
                    <?php echo $project->comment; ?>
                </p>

                <form method="post" action="/admin/checking/<?php echo $this['action']; ?>/<?php echo $project->id; ?>/?filter=<?php echo $this['filter']; ?>">

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

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';