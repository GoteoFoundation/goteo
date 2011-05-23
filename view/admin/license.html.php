<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

$filters = $this['filters'];

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

            <?php if (!empty($filters['group'])) : ?>
                <p>Viendo las licencias de agrupación '<?php echo $this['groups'][$filters['group']]; ?>'</p>
            <?php endif;?>

            <?php if (!empty($filters['icon'])) : ?>
                <p>Viendo las licencias para tipo '<?php echo $this['icons'][$filters['icon']]->name; ?>'</p>
            <?php endif;?>

            <form id="filter-form" action="/admin/licenses" method="get">
                <label for="group-filter">Mostrar las licencias por grupo:</label>
                <select id="group-filter" name="group" onchange="document.getElementById('filter-form').submit();">
                    <option value="">Todos los grupos</option>
                <?php foreach ($this['groups'] as $groupId=>$groupName) : ?>
                    <option value="<?php echo $groupId; ?>"<?php if ($filters['group'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
                <?php endforeach; ?>
                </select>
                <label for="icon-filter">Mostrar las licencias asociadas a un tipo de retorno:</label>
                <select id="icon-filter" name="icon" onchange="document.getElementById('filter-form').submit();">
                    <option value="">Todos los tipos</option>
                <?php foreach ($this['icons'] as $icon) : ?>
                    <option value="<?php echo $icon->id; ?>"<?php if ($filters['icon'] == $icon->id) echo ' selected="selected"';?>><?php echo $icon->name; ?></option>
                <?php endforeach; ?>
                </select>
            </form>

            <p><a href="?filter=<?php echo serialize($filters); ?>&add">Añadir licencia</a></p>

                    <table>
                        <thead>
                            <tr>
                                <td>Nombre</td> <!-- name -->
                                <td>Agrupación</td> <!-- group -->
                                <td>Posición</td> <!-- order -->
                                <td><!-- Move up --></td>
                                <td><!-- Move down --></td>
                                <td><!-- Edit --></td>
<!--                                <td> Remove </td> -->
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($this['licenses'] as $license) : ?>
                            <tr>
                                <td><?php echo $license->name; ?></td>
                                <td><?php echo !empty($license->group) ? $this['groups'][$license->group] : ''; ?></td>
                                <td><?php echo $license->order; ?></td>
                                <td><a href="?filter=<?php echo $filters['group']; ?>&up=<?php echo $license->id; ?>">[&uarr;]</a></td>
                                <td><a href="?filter=<?php echo $filters['group']; ?>&down=<?php echo $license->id; ?>">[&darr;]</a></td>
                                <td><a href="?filter=<?php echo $filters['group']; ?>&edit=<?php echo $license->id; ?>">[Editar]</a></td>
<!--                                <td><a href="?filter=<?php echo $filters['group']; ?>&remove=<?php echo $license->id; ?>">[Quitar]</a></td>  -->
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';