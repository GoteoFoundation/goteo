<?php

use Goteo\Library\Text;

$bodyClass = 'project-show';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Tipos de Retornos/Recompensas</h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="needs"><a href="/admin/checking">Revisión de proyectos</a></li>
                    </ul>
                </div>
            </div>

        </div>
            <!-- <p><a href="?filter=<?php echo $this['filter']; ?>&add">Añadir tipo</a></p> -->

        <div id="main">
            <?php if (!empty($this['filter'])) : ?>
                <h3>Viendo los tipos para el grupo '<?php echo $this['groups'][$this['filter']]; ?>'</h3>
            <?php endif;?>

            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['success'])) {
                echo '<pre>' . print_r($this['success'], 1) . '</pre>';
            } ?>

            <div class="widget">
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

            <div class="widget">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th> <!-- name -->
                            <th>Agrupación</th> <!-- group -->
                            <th>Editar</th>
<!--                        <th> Remove </th>  -->
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($this['icons'] as $icon) : ?>
                        <tr>
                            <td><?php echo $icon->name; ?></td>
                            <td><?php echo !empty($icon->group) ? $this['groups'][$icon->group] : 'Ambas'; ?></td>
                            <td><a href="?filter=<?php echo $this['filter']; ?>&edit=<?php echo $icon->id; ?>">[Edit]</a></td>
                            <!-- <td><a href="?filter=<?php echo $this['filter']; ?>&remove=<?php echo $icon->id; ?>">[Quitar]</a></td> -->
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';