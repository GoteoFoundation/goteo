<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&icon={$filters['icon']}";

$status = Goteo\Model\Project::status();

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Gestión de retornos colectivos</h2>
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

        <div id="main">
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <div class="widget board">
                <form id="filter-form" action="/admin/rewards" method="get">
                    <label for="status-filter">Mostrar por estado:</label>
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todos los estados</option>
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>

                    <label for="icon-filter">Mostrar retornos del tipo:</label>
                    <select id="icon-filter" name="icon" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todos los tipos</option>
                    <?php foreach ($this['icons'] as $iconId=>$iconName) : ?>
                        <option value="<?php echo $iconId; ?>"<?php if ($filters['icon'] == $iconId) echo ' selected="selected"';?>><?php echo $iconName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="widget board">
                <?php if (!empty($this['projects'])) : ?>
                <?php foreach ($this['projects'] as $project) : ?>

                    <?php if (empty($project->social_rewards)) continue; ?>
                
                    <h3><?php echo $project->name; ?></h3>
                    <p><span><?php echo $status[$project->status]; ?></span></p>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Retorno</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($project->social_rewards as $reward) : ?>
                            <tr>
                                <td><?php echo $reward->reward; ?></td>
                                <td><?php echo $this['icons'][$reward->icon]; ?></td>
                                <td><?php echo $reward->fulfilled ? 'Cumplido' : 'Pendiente'; ?></td>
                                <?php if (!$reward->fulfilled) : ?>
                                <td><a href="<?php echo "/admin/rewards/fulfill/{$reward->id}{$filter}"; ?>">[Dar por cumplido]</a></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>

                    <hr />

                    <?php endforeach; ?>
                <?php else : ?>
                <p>No se han encontrado registros</p>
                <?php endif; ?>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';