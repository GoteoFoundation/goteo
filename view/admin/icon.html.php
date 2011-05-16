<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Tipos de Retornos/Recompensas</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>

            <?php if (!empty($this['success'])) : ?>
                <p><span style="color:green;"><?php echo $this['success']; ?></span><br /></p>
            <?php endif;?>

            <?php if (!empty($this['filter'])) : ?>
                <p>Viendo los tipos para el grupo '<?php echo $this['groups'][$this['filter']]; ?>'</p>
            <?php endif;?>

            <form id="groupfilter-form" action="/admin/icons" method="get">
                <label for="group-filter">Mostrar los tipos para:</label>
                <select id="group-filter" name="filter" onchange="document.getElementById('groupfilter-form').submit();">
                    <option value="">Todo</option>
                <?php foreach ($this['groups'] as $groupId=>$groupName) : ?>
                    <option value="<?php echo $groupId; ?>"<?php if ($this['filter'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
                <?php endforeach; ?>
                </select>
            </form>

            <!-- <p><a href="?filter=<?php echo $this['filter']; ?>&add">Añadir tipo</a></p> -->

                    <table>
                        <thead>
                            <tr>
                                <td>Nombre</td> <!-- name -->
                                <td>Agrupación</td> <!-- group -->
                                <td><!-- Edit --></td>
<!--                                <td> Remove </td>  -->
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($this['icons'] as $icon) : ?>
                            <tr>
                                <td><?php echo $icon->name; ?></td>
                                <td><?php echo !empty($icon->group) ? $this['groups'][$icon->group] : 'Ambas'; ?></td>
                                <td><a href="?filter=<?php echo $this['filter']; ?>&edit=<?php echo $icon->id; ?>">[Editar]</a></td>
                                <!-- <td><a href="?filter=<?php echo $this['filter']; ?>&remove=<?php echo $icon->id; ?>">[Quitar]</a></td> -->
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';