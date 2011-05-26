<?php

use Goteo\Library\Text;

$bodyClass = 'project-show';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Preguntas frecuentes</h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="needs"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/admin/faq?filter=<?php echo $this['filter']; ?>">Preguntas frecuentes</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h3>Añadiendo nueva pregunta frecuente</h3>
                    <?php break;
                case 'edit': ?>
                    <h3>Editando la pregunta '<?php echo $this['faq']->title; ?>'</h3>
                    <?php break;
            } ?>

            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <div class="widget">
                <form method="post" action="/admin/faq?filter=<?php echo $this['filter']; ?>">

                    <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
                    <input type="hidden" name="id" value="<?php echo $this['faq']->id; ?>" />
                    <input type="hidden" name="order" value="<?php echo $this['faq']->order; ?>" />

                    <label for="faq-section">Sección:</label><br />
                    <select id="faq-section" name="section">
                        <?php foreach ($this['sections'] as $id=>$name) : ?>
                        <option value="<?php echo $id; ?>"<?php if ($id == $this['faq']->section) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
    <br />
                    <label for="faq-title">Título:</label><br />
                    <input type="text" name="title" id="faq-title" value="<?php echo $this['faq']->title; ?>" />
    <br />
                    <label for="faq-description">Descripción:</label><br />
                    <textarea name="description" id="faq-description" cols="60" rows="10"><?php echo $this['faq']->description; ?></textarea>



                    <input type="submit" name="save" value="Guardar" />
                </form>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';