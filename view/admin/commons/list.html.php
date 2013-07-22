<?php
use Goteo\Library\Text;

$filters = $this['filters'];
$status = $this['statuses'];
?>
<div class="widget board">
    <form id="filter-form" action="/admin/commons" method="get">
        <div style="float:left;margin:5px;">
            <label for="projStatus-filter">Solo proyectos en estado:</label><br />
            <select id="projStatus-filter" name="projStatus">
                <option value="">Cualquier exitoso</option>
            <?php foreach ($this['projStatus'] as $Id=>$Name) : ?>
                <option value="<?php echo $Id; ?>"<?php if ($filters['projStatus'] == $Id) echo ' selected="selected"';?>><?php echo $Name; ?></option>
            <?php endforeach; ?>
            </select><br />
            <span style="font-size: 10px;">Afecta al filtro Proyecto</span>
        </div>

        <div style="float:left;margin:5px;">
            <label for="projects-filter">Proyecto:</label><br />
            <select id="projects-filter" name="project" >
                <option value="">Todos los proyectos</option>
            <?php foreach ($this['projects'] as $project) : ?>
                <option value="<?php echo $project->id; ?>"<?php if ($filters['project'] === $project->id) echo ' selected="selected"';?> status="<?php echo $project->status; ?>"><?php echo $project->name; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        
        <div style="float:left;margin:5px;">
            <label for="status-filter">Mostrar por estado del retorno:</label><br />
            <select id="status-filter" name="status" >
                <option value="">Cualquier estado</option>
            <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="icon-filter">Mostrar retornos del tipo:</label><br />
            <select id="icon-filter" name="icon" >
                <option value="">Todos los tipos</option>
            <?php foreach ($this['icons'] as $iconId=>$iconName) : ?>
                <option value="<?php echo $iconId; ?>"<?php if ($filters['icon'] == $iconId) echo ' selected="selected"';?>><?php echo $iconName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/commons/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (!empty($this['projects'])) : ?>
    <?php foreach ($this['projects'] as $project) : ?>

        <?php if (!empty($filters['project']) && $project->id != $filters['project']) {
                continue;
            }
        ?>

        <h3><?php echo $project->name; ?> (<?php echo $status[$project->status]; ?>)</h3>
        <?php 
        if (empty($project->social_rewards)) {
            echo '<p>Este proyecto no tiene retornos colectivos</p><hr />';
            continue; 
        }
        ?>

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
                    <?php if (!$reward->fulsocial) : ?>
                    <td style="color: red;" >Pendiente</td>
                    <td><a href="<?php echo "/admin/commons/fulfill/{$reward->id}"; ?>">[Dar por cumplido]</a></td>
                    <?php else : ?>
                    <td style="color: green;" >Cumplido</td>
                    <td><a href="<?php echo "/admin/commons/unfill/{$reward->id}"; ?>">[Dar por pendiente]</a></td>
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
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        
        // al filtrar por estado de proyecto
        $("#projStatus-filter").change(function(){
            
            $("#filter-form").submit();
        });
        
    });
</script>