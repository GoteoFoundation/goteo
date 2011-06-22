<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Tipos de Retornos/Recompensas</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/admin/icons?filter=<?php echo $this['filter']; ?>">Tipos de Retorno/Recompensa</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h3>Añadiendo nuevo tipo</h3>
                    <?php break;
                case 'edit': ?>
                    <h3>Editando el tipo '<?php echo $this['icon']->name; ?>'</h3>
                    <?php break;
            } ?>

            <?php if (!empty($this['errors']) || !empty($this['success'])) : ?>
                <div class="widget">
                    <p>
                        <?php echo implode(',', $this['errors']); ?>
                        <?php echo implode(',', $this['success']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="widget board">
                <!-- super form -->
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

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';