<?php

use Goteo\Library\Text;

$filters = $this['filters'];

$bodyClass = 'project-show';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Licencias</h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="needs"><a href="/admin/checking">Revisión de proyectos</a></li>
<!--            <li><a href="?filter=<?php echo serialize($filters); ?>&add">Añadir licencia</a></li> -->
                    </ul>
                </div>
            </div>


        </div>

        <div id="main">
            <?php if (!empty($filters['group'])) : ?>
                <p>Viendo las licencias de agrupación '<?php echo $this['groups'][$filters['group']]; ?>'</p>
            <?php endif;?>

            <?php if (!empty($filters['icon'])) : ?>
                <p>Viendo las licencias para tipo '<?php echo $this['icons'][$filters['icon']]->name; ?>'</p>
            <?php endif;?>

            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['success'])) {
                echo '<pre>' . print_r($this['success'], 1) . '</pre>';
            } ?>

            <div class="widget">
                <form id="filter-form" action="/admin/licenses" method="get">
                    <label for="group-filter">Mostrar por grupo:</label>
                    <select id="group-filter" name="group" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todos los grupos</option>
                    <?php foreach ($this['groups'] as $groupId=>$groupName) : ?>
                        <option value="<?php echo $groupId; ?>"<?php if ($filters['group'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
                    <?php endforeach; ?>
                    </select>

                    <label for="icon-filter">Mostrar por tipo de retorno:</label>
                    <select id="icon-filter" name="icon" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todos los tipos</option>
                    <?php foreach ($this['icons'] as $icon) : ?>
                        <option value="<?php echo $icon->id; ?>"<?php if ($filters['icon'] == $icon->id) echo ' selected="selected"';?>><?php echo $icon->name; ?></option>
                    <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="widget">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th> <!-- name -->
                            <th>Agrupación</th> <!-- group -->
                            <th>Posición</th> <!-- order -->
                            <th><!-- Move up --></th>
                            <th><!-- Move down --></th>
                            <th><!-- Edit --></th>
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
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';