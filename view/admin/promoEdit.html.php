<?php

use Goteo\Library\Text;

$bodyClass = 'project-show';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Proyectos destacados</h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="needs"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/admin/promote">Destacados</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h3>Añadiendo nuevo proyecto destacado</h3>
                    <?php break;
                case 'edit': ?>
                    <h3>Editando el proyecto destacado '<?php echo $this['promo']->name; ?>'</h3>
                    <?php break;
            } ?>

            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <div class="widget">
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

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';