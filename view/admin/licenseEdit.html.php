<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h2>Añadiendo nueva licencia</h2>
                    <?php break;
                case 'edit': ?>
                    <h2>Editando la licencia '<?php echo $this['license']->name; ?>'</h2>
                    <?php break;
            } ?>

            <p><a href="/admin">Volver al Menú de administración</a></p>
            <p><a href="/admin/licenses?filter=<?php echo serialize($filters); ?>">Volver a la lista de licencias</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;
            ?>

            <form method="post" action="/admin/licenses?filter=<?php echo serialize($filters); ?>">

                <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
                <input type="hidden" name="id" value="<?php echo $this['license']->id; ?>" />
                <input type="hidden" name="order" value="<?php echo $this['license']->order; ?>" />

                <label for="license-group">Grupo:</label><br />
                <select id="license-group" name="group">
                    <option value="">Ninguna</option>
                    <?php foreach ($this['groups'] as $id=>$name) : ?>
                    <option value="<?php echo $id; ?>"<?php if ($id == $this['license']->group) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
<br />
                <label for="license-name">Nombre:</label><br />
                <input type="text" name="name" id="license-name" value="<?php echo $this['license']->name; ?>" />
<br />
                <label for="license-description">Descripción:</label><br />
                <textarea name="description" id="license-description" cols="60" rows="10"><?php echo $this['license']->description; ?></textarea>
<br />
                <label for="license-icons">Tipos:</label><br />
                <select id="license-icons" name="icons[]" multiple size="6">
                    <?php foreach ($this['icons'] as $icon) : ?>
                    <option value="<?php echo $icon->id; ?>"<?php if (in_array($icon->id, $this['license']->icons)) echo ' selected="selected"'; ?>><?php echo $icon->name; ?></option>
                    <?php endforeach; ?>
                </select>


                <input type="submit" name="save" value="Guardar" />
            </form>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';