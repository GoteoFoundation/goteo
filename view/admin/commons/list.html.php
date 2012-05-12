<?php

use Goteo\Library\Text;

$filters = $this['filters'];

$status = Goteo\Model\Project::status();

?>
<div class="widget board">
    <form id="filter-form" action="/admin/commons" method="get">
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
                    <td><?php echo $reward->fulsocial ? 'Cumplido' : 'Pendiente'; ?></td>
                    <?php if (!$reward->fulsocial) : ?>
                    <td><a href="<?php echo "/admin/rewards/fulfill/{$reward->id}"; ?>">[Dar por cumplido]</a></td>
                    <?php else : ?>
                    <td><a href="<?php echo "/admin/rewards/unfill/{$reward->id}"; ?>">[Dar por pendiente]</a></td>
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