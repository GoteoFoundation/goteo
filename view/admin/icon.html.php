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
                    </ul>
                </div>
            </div>

        </div>
            <!-- <p><a href="/admin/icons/add/?filter=<?php echo $this['filter']; ?>">Añadir tipo</a></p> -->

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
                <form id="groupfilter-form" action="/admin/icons" method="get">
                    <label for="group-filter">Mostrar los tipos para:</label>
                    <select id="group-filter" name="filter" onchange="document.getElementById('groupfilter-form').submit();">
                        <option value="">Todo</option>
                    <?php foreach ($this['groups'] as $groupId=>$groupName) : ?>
                        <option value="<?php echo $groupId; ?>"<?php if ($this['filter'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="widget board">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th> <!-- name -->
                            <th>Tooltip</th> <!-- descripcion -->
                            <th>Agrupación</th> <!-- group -->
                            <th><!-- Editar --></th>
<!--                        <th> Remove </th>  -->
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($this['icons'] as $icon) : ?>
                        <tr>
                            <td><?php echo $icon->name; ?></td>
                            <td><?php echo $icon->description; ?></td>
                            <td><?php echo !empty($icon->group) ? $this['groups'][$icon->group] : 'Ambas'; ?></td>
                            <td><a href="/admin/icons/edit/<?php echo $icon->id; ?>/?filter=<?php echo $this['filter']; ?>">[Edit]</a></td>
                            <!-- <td><a href="/admin/icons/remove/<?php echo $icon->id; ?>/?filter=<?php echo $this['filter']; ?>">[Quitar]</a></td> -->
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';