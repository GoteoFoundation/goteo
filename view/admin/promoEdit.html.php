<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h2>Añadiendo nuevo proyecto destacado</h2>
                    <?php break;
                case 'edit': ?>
                    <h2>Editando el proyecto destacado '<?php echo $this['promo']->name; ?>'</h2>
                    <?php break;
            } ?>

            <p><a href="/admin">Volver al Menú de administración</a></p>
            <p><a href="/admin/promote">Volver a la lista de proyectos destacados</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;
            ?>

            <form method="post" action="/admin/promote">

                <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
                <input type="hidden" name="order" value="<?php echo $this['promo']->order; ?>" />

                <!-- Selector (radio) de proyectos  para el add -->
                <?php if ($this['action'] == 'add') : ?>
                <label for="promote-project">Proyecto:</label><br />
                <select id="promote-project" name="project">
                    <option value="">Seleccionar el proyecto</option>
                    <?php foreach ($this['projects'] as $project) : ?>
                    <option value="<?php echo $project->id; ?>"<?php if ($project->id == $this['promo']->project) echo ' selected="selected"'; ?>><?php echo $project->name; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php else : ?>
                <input type="hidden" name="project" value="<?php echo $this['promo']->project; ?>" />
                <?php endif; ?>
<br />
                <label for="promote-title">Título:</label><br />
                <input type="text" name="title" id="promote-title" value="<?php echo $this['promo']->title; ?>" />
<br />
                <label for="promote-description">Descripción:</label><br />
                <textarea name="description" id="promote-description" cols="60" rows="10"><?php echo $this['promo']->description; ?></textarea>



                <input type="submit" name="save" value="Guardar" />
            </form>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';