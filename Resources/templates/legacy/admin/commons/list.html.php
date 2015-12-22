<?php
use Goteo\Library\Text,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$filters = $vars['filters'];
$status = $vars['statuses'];

$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters .= "&{$key}={$value}";
}

$pagedResults = new Paginated($vars['projects'], 10, isset($_GET['page']) ? $_GET['page'] : 1);

//para autocomplete
$items = array();

foreach ($vars['projects'] as $project) {
    $items[] = '{ value: "'.str_replace('"','\"',$project->name).'", id: "'.$project->id.'" }';
        if($filters['project'] === $project->name) $preval=$project->name;
}


?>
<div class="widget board">
    <form id="filter-form" action="/admin/commons" method="get">
        <div style="float:left;margin:5px;">
            <label for="projStatus-filter">Solo proyectos en estado:</label><br />
            <select id="projStatus-filter" name="projStatus">
                <option value="">Cualquier exitoso</option>
            <?php foreach ($vars['projStatus'] as $Id=>$Name) : ?>
                <option value="<?php echo $Id; ?>"<?php if ($filters['projStatus'] == $Id) echo ' selected="selected"';?>><?php echo $Name; ?></option>
            <?php endforeach; ?>
            </select><br />
            <span style="font-size: 10px;">Afecta al filtro Proyecto</span>
        </div>

        <div style="float:left;margin:5px;">
            <label for="projects-filter">Proyecto: (autocomplete nombre)</label><br />
            <input type="text" name="project" id="projects-filter" value="<?php if ($filters['project'] === $preval) echo $preval;?>" size="60" />
        </div>

        <?php /*
 * estos filtros ya no tienen sentido
  *
        <div style="float:left;margin:5px;">
            <label for="status-filter">Mostrar por estado del retorno:</label><br />
            <select id="status-filter" name="status" >
                <option value="">Cualquier estado</option>
            <?php foreach ($vars['status'] as $statusId=>$statusName) : ?>
                <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="icon-filter">Mostrar retornos del tipo:</label><br />
            <select id="icon-filter" name="icon" >
                <option value="">Todos los tipos</option>
            <?php foreach ($vars['icons'] as $iconId=>$iconName) : ?>
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
    <a href="/admin/commons?reset=filters">[<?= $this->text('admin-remove-filters') ?>]</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (!empty($vars['projects'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Proyecto</th>
                <th>Estado</th>
                <th>Cumplidos</th>
                <th>Vencimiento</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php  while ($project = $pagedResults->fetchPagedRow()) :

            // calculo fecha de vencimiento (timestamp de un año despues de financiado)
            $deadline = mktime(0, 0, 0,
                date('m', strtotime($project->success)),
                date('d', strtotime($project->success)),
                date('Y', strtotime($project->success)) + 1
            );

            ?>
            <tr>
                <td><a href="/project/<?php echo $project->id?>" target="blank"><?php echo $project->name; ?></a></td>
                <td><?php echo $status[$project->status]; ?></td>
                <td style="text-align: center;"><?php echo $project->cumplidos.'/'.count($project->social_rewards); ?></td>
                <td><?php echo date('d-m-Y', $deadline); ?></td>
                <td>
                    <a href="/admin/commons/view/<?php echo $project->id?>">[Gestionar]</a>&nbsp;
                    <a href="/admin/commons/info/<?php echo $project->id?>">[Ver Contacto]</a>&nbsp;
                    <?php if ($project->status == 4) : ?><a href="<?php echo "/admin/commons/fulfill/{$project->id}"; ?>" onclick="return confirm('Se va a cambiar el estado del proyecto, ok?');">[Cumplido]</a>&nbsp;<?php endif; ?>
                    <a href="/admin/projects?proj_id=<?php echo $project->id?>" target="blank">[Admin]</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
 <ul id="pagination">
    <?php   $pagedResults->setLayout(new DoubleBarLayout());
        echo $pagedResults->fetchPagedNavigation($the_filters); ?>
 </ul>
    <?php else : ?>
    <p>No se han encontrado registros</p>
</div>
    <?php endif; ?>
<script type="text/javascript">
$(function () {

    var items = [<?php echo implode(', ', $items); ?>];

    /* Autocomplete para elementos */
    $( "#projects-filter" ).autocomplete({
      source: items,
      minLength: 1,
      autoFocus: true,
      select: function( event, ui) {
                $("#item").val(ui.item.id);
            }
    });

});
</script>
