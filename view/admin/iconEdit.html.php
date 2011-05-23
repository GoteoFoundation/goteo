<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h2>Añadiendo nuevo tipo</h2>
                    <?php break;
                case 'edit': ?>
                    <h2>Editando el tipo '<?php echo $this['icon']->name; ?>'</h2>
                    <?php break;
            } ?>

            <p><a href="/admin">Volver al Menú de administración</a></p>
            <p><a href="/admin/icons?filter=<?php echo $this['filter']; ?>">Volver a la lista de tipos de Retorno/Recompensa</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;
            ?>

            <form method="post" action="/admin/icons?filter=<?php echo $this['filter']; ?>">

                <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
                <input type="hidden" name="id" value="<?php echo $this['icon']->id; ?>" />

                <label for="icon-group">Agrupación:</label><br />
                <select id="icon-group" name="group">
                    <option value="">Ambas</option>
                    <?php foreach ($this['groups'] as $id=>$name) : ?>
                    <option value="<?php echo $id; ?>"<?php if ($id == $this['icon']->group) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
<br />
                <label for="icon-name">Nombre:</label><br />
                <input type="text" name="name" id="icon-name" value="<?php echo $this['icon']->name; ?>" />
<br />
                <label for="icon-description">Descripción:</label><br />
                <textarea name="description" id="icon-description" cols="60" rows="10"><?php echo $this['icon']->description; ?></textarea>



                <input type="submit" name="save" value="Guardar" />
            </form>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';