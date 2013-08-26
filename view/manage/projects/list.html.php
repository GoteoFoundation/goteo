<?php
use Goteo\Library\Text;

$filters = $this['filters'];
?>
<div class="widget board">
    <form id="filter-form" action="/manage/projects" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del autor:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>
        
        <div style="float:left;margin:5px;">
            <label for="proj_name-filter">Nombre del proyecto:</label><br />
            <input id="proj_name-filter" name="proj_name" value="<?php echo $filters['proj_name']; ?>" style="width:250px"/>
        </div>
        
        <br clear="both" />
        
        <div style="float:left;margin:5px;">
            <label for="status-filter">Estado de proyecto:</label><br />
            <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                <option value="-1"<?php if ($filters['status'] == -1) echo ' selected="selected"';?>>Todos los financiados</option>
            <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        
        <div style="float:left;margin:5px;">
            <label for="projects-filter">Estado de campaña</label><br />
            <select id="projects-filter" name="projectStatus" onchange="document.getElementById('filter-form').submit();">
                <option value="all"<?php echo ($filters['projectStatus'] == 'all') ? ' selected="selected"' : ''; ?>>En campaña o financiados</option>
                <option value="goingon"<?php echo ($filters['projectStatus'] == 'goingon') ? ' selected="selected"' : ''; ?>>En campa&ntilde;a</option>
                <option value="passed"<?php echo ($filters['projectStatus'] == 'passed') ? ' selected="selected"' : ''; ?>>Pasado la primera ronda</option>
                <option value="succed"<?php echo ($filters['projectStatus'] == 'succed') ? ' selected="selected"' : ''; ?>>Terminado la segunda ronda</option>
            </select>
        </div>
        <div style="float:left;margin:5px;">
            <label for="contract-filter">Estado de contrato</label><br />
            <select id="contract-filter" name="contractStatus" onchange="document.getElementById('filter-form').submit();">
                <option value="all"<?php echo ($filters['contractStatus'] == 'all') ? ' selected="selected"' : ''; ?>>En cualquier estado</option>
                <option value="none"<?php echo ($filters['contractStatus'] == 'none') ? ' selected="selected"' : ''; ?>>Sin rellenar</option>
                <option value="filled"<?php echo ($filters['contractStatus'] == 'filled') ? ' selected="selected"' : ''; ?>>Contrato rellenado</option>
                <option value="sended"<?php echo ($filters['contractStatus'] == 'sended') ? ' selected="selected"' : ''; ?>>Contrato enviado</option>
                <option value="checked"<?php echo ($filters['contractStatus'] == 'checked') ? ' selected="selected"' : ''; ?>>Contrato revisado</option>
                <option value="ready"<?php echo ($filters['contractStatus'] == 'ready') ? ' selected="selected"' : ''; ?>>Documento generado</option>
            </select>
        </div>
        
        <br clear="both" />
        
        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
        
        <div style="float:left;margin:5px;">
            <label for="order-filter">Ordenar por:</label><br />
            <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
            <?php foreach ($this['orders'] as $orderId=>$orderName) : ?>
                <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        
    </form>
    <br clear="both" />
    <a href="/manage/projects/?reset=filters">Quitar filtros</a>
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (empty($this['projects'])) : ?>
    <p>No se han encontrado registros</p>
<?php else: ?>
    <p><strong>OJO!</strong> Resultado limitado a 999 registros como máximo.</p>
<?php endif; ?>
</div>

    
<?php foreach ($this['projects'] as $project) : ?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th style="width: 250px;">Proyecto</th> <!-- edit -->
                <th style="min-width: 150px;">Creador</th> <!-- mailto -->
                <th style="min-width: 80px;">Estado proyecto</th>
                <th style="min-width: 80px;">Estado contrato</th>
                <th>Ronda</th>
                <th>Inicio 1a</th>
                <th>Final 1a</th>
                <th>Final 2a</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td><a href="/project/<?php echo $project->id; ?>" target="_blank" title="Preview" style="<?php if (isset($project->called)) echo 'color: blue;'; ?>"><?php echo $project->name; ?></a></td>
                <td><a href="mailto:<?php echo $project->user->email; ?>"><?php echo substr($project->user->email, 0, 100); ?></a></td>
            </tr>
            <tr>
                <td colspan="7"><?php 
                    if ($project->status == 3 && $project->round > 0) echo "Le quedan {$project->days} días de la {$project->round}ª ronda.&nbsp;&nbsp;&nbsp;<strong>Conseguido:</strong> ".\amount_format($project->invested)."€&nbsp;&nbsp;&nbsp;<strong>Cofin:</strong> {$project->num_investors}&nbsp;&nbsp;&nbsp;<strong>Colab:</strong> {$project->num_messegers}"; 
                ?></td>
            </tr>
            <tr>
                <td colspan="7">
                    IR A:&nbsp;
                    <a href="/contract/<?php echo $project->id; ?>" target="_blank">[Texto contrato]</a>
                    <a href="/contract/edit/<?php echo $project->id; ?>" target="_blank">[Formulario contrato]</a>
                    <a href="/admin/users/?id=<?php echo $project->owner; ?>" target="_blank">[Impulsor]</a>
                    <a href="/admin/accounts/?projects=<?php echo $project->id; ?>" title="Ver sus aportes">[Aportes]</a>
                    <a href="/admin/projects/report/<?php echo $project->id; ?>" target="_blank">[Informe Financiacion]</a>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    CAMBIAR:&nbsp;
                    <a href="<?php echo "/manage/projects/accounts/{$project->id}"; ?>">[Cuentas]</a>
                    &nbsp;|&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    PROCESO:&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    INCIDENCIAS:&nbsp;
                </td>
            </tr>
        </tbody>

    </table>
</div>
<?php endforeach; ?>
