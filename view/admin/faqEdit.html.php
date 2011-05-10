<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h2>Añadiendo nueva pregunta frecuente</h2>
                    <?php break;
                case 'edit': ?>
                    <h2>Editando la pregunta '<?php echo $this['faq']->title; ?>'</h2>
                    <?php break;
            } ?>

            <p><a href="/admin">Volver al Menú de administración</a></p>
            <p><a href="/admin/faq?section=<?php echo $this['faq']->section; ?>">Volver a la lista de preguntas frecuentes</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;
            ?>

            <form method="post" action="/admin/faq?section=<?php echo $this['faq']->section; ?>">

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

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';