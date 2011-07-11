<?php

use Goteo\Library\Text;

$filters = $this['filters'];

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Licencias</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
<!--            <li><a href="/add/?filter=<?php echo $filter; ?>">Añadir licencia</a></li> -->
                    </ul>
                </div>
            </div>


        </div>

        <div id="main">
            <?php if (!empty($this['errors']) || !empty($this['success'])) : ?>
                <div class="widget">
                    <p>
                        <?php echo implode(',', $this['errors']); ?>
                        <?php echo implode(',', $this['success']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="widget board">
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

            <div class="widget board">
                <?php if (!empty($this['licenses'])) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th> <!-- name -->
                            <th>Tooltip</th> <!-- description -->
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
                            <td><?php echo $license->description; ?></td>
                            <td><?php echo !empty($license->group) ? $this['groups'][$license->group] : ''; ?></td>
                            <td><?php echo $license->order; ?></td>
                            <td><a href="/admin/licenses/up/<?php echo $license->id; ?>/?filter=<?php echo $filters['group']; ?>">[&uarr;]</a></td>
                            <td><a href="/admin/licenses/down/<?php echo $license->id; ?>/?filter=<?php echo $filters['group']; ?>">[&darr;]</a></td>
                            <td><a href="/admin/licenses/edit/<?php echo $license->id; ?>/?filter=<?php echo $filters['group']; ?>">[Editar]</a></td>
<!--                                <td><a href="/admin/licenses/remove=<?php echo $license->id; ?>/?filter=<?php echo $filters['group']; ?>">[Quitar]</a></td>  -->
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
                <?php else : ?>
                <p>No se han encontrado registros</p>
                <?php endif; ?>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';