<?php
use Goteo\Core\View,
    Goteo\Library\Text;

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

        <?php /*
 * estos filtros ya no tienen sentido
  *
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

         */ ?>

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
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Proyecto</th>
                <th>Estado</th>
                <th>Cumplidos</th>
                <th>Vencimiento</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this['projects'] as $project) :

            // calculo fecha de vencimiento (timestamp de un año despues de financiado)
            $deadline = mktime(0, 0, 0,
                date('m', strtotime($project->success)),
                date('d', strtotime($project->success)),
                date('Y', strtotime($project->success)) + 1
            );

            ?>
            <tr>
                <td><a href="/admin/projects/?proj_id=<?php echo $project->id?>" target="blank">[Admin]</a></td>
                <td><a href="/project/<?php echo $project->id?>" target="blank"><?php echo $project->name; ?></a></td>
                <td><?php echo $status[$project->status]; ?></td>
                <td style="text-align: center;"><?php echo $project->cumplidos.'/'.count($project->social_rewards); ?></td>
                <td><?php echo date('d-m-Y', $deadline); ?></td>
                <td><a href="/admin/commons/view/<?php echo $project->id?>">[Gestionar]</a></td>
                <td><a href="/project/edit/<?php echo $project->id?>/rewards" target="blank">[Modificar]</a></td>
                <td>
                    <?php if ($project->status == 4) : ?><a href="<?php echo "/admin/commons/fulfill/{$project->id}"; ?>" onclick="return confirm('Se va a cambiar el estado del proyecto, ok?');">[Retorno Cumplido]</a><?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>