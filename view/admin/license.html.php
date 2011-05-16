<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Licencias</h2>

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
                <p>Viendo las licencias de agrupación '<?php echo $this['groups'][$this['filter']]; ?>'</p>
            <?php endif;?>

            <form id="groupfilter-form" action="/admin/licenses" method="get">
                <label for="group-filter">Mostrar las licencias por agrupación:</label>
                <select id="group-filter" name="filter" onchange="document.getElementById('groupfilter-form').submit();">
                    <option value="">Todas</option>
                <?php foreach ($this['groups'] as $groupId=>$groupName) : ?>
                    <option value="<?php echo $groupId; ?>"<?php if ($this['filter'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
                <?php endforeach; ?>
                </select>
            </form>

            <p><a href="?filter=<?php echo $this['filter']; ?>&add">Añadir licencia</a></p>

                    <table>
                        <thead>
                            <tr>
                                <td>Nombre</td> <!-- name -->
                                <td>Agrupación</td> <!-- group -->
                                <td>Posición</td> <!-- order -->
                                <td><!-- Move up --></td>
                                <td><!-- Move down --></td>
                                <td><!-- Edit --></td>
                                <td><!-- Remove --></td>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($this['licenses'] as $license) : ?>
                            <tr>
                                <td><?php echo $license->name; ?></td>
                                <td><?php echo !empty($license->group) ? $this['groups'][$license->group] : ''; ?></td>
                                <td><?php echo $license->order; ?></td>
                                <td><a href="?filter=<?php echo $this['filter']; ?>&up=<?php echo $license->id; ?>">[&uarr;]</a></td>
                                <td><a href="?filter=<?php echo $this['filter']; ?>&down=<?php echo $license->id; ?>">[&darr;]</a></td>
                                <td><a href="?filter=<?php echo $this['filter']; ?>&edit=<?php echo $license->id; ?>">[Editar]</a></td>
                                <td><a href="?filter=<?php echo $this['filter']; ?>&remove=<?php echo $license->id; ?>">[Quitar]</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';