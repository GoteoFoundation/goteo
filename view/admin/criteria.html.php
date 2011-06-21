<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Criterios de puntuación</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/admin/criteria/add/?filter=<?php echo $this['filter']; ?>">Añadir criterio</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['success'])) {
                echo '<pre>' . print_r($this['success'], 1) . '</pre>';
            } ?>

            <div class="widget board">
                <form id="sectionfilter-form" action="/admin/criteria" method="get">
                    <label for="section-filter">Mostrar los criterios de la sección:</label>
                    <select id="section-filter" name="filter" onchange="document.getElementById('sectionfilter-form').submit();">
                    <?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
                        <option value="<?php echo $sectionId; ?>"<?php if ($this['filter'] == $sectionId) echo ' selected="selected"';?>><?php echo $sectionName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="widget board">
                <table>
                    <thead>
                        <tr>
                            <th>Título</th> <!-- title -->
                            <th>Posición</th> <!-- order -->
                            <td><!-- Move up --></td>
                            <td><!-- Move down --></td>
                            <td><!-- Edit --></td>
                            <td><!-- Remove --></td>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($this['criterias'] as $criteria) : ?>
                        <tr>
                            <td><?php echo $criteria->title; ?></td>
                            <td><?php echo $criteria->order; ?></td>
                            <td><a href="/admin/criteria/up/<?php echo $criteria->id; ?>/?filter=<?php echo $this['filter']; ?>">[&uarr;]</a></td>
                            <td><a href="/admin/criteria/down/<?php echo $criteria->id; ?>/?filter=<?php echo $this['filter']; ?>">[&darr;]</a></td>
                            <td><a href="/admin/criteria/edit/<?php echo $criteria->id; ?>/?filter=<?php echo $this['filter']; ?>">[Editar]</a></td>
                            <td><a href="/admin/criteria/remove/<?php echo $criteria->id; ?>/?filter=<?php echo $this['filter']; ?>">[Quitar]</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
                
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';