<?php
use Goteo\Library\Text,
    Goteo\Model\Invest;

$filters = $this['filters'];

?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/contracts" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <div style="float:left;margin:5px;">
            <label for="projects-filter">Proyecto</label><br />
            <select id="projects-filter" name="project" onchange="document.getElementById('filter-form').submit();">
                <option value="">Todos los proyectos</option>
            <?php foreach ($this['projects'] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters['projects'] === (string) $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/contracts/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro</p>
<?php elseif (!empty($this['list'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Número</th>
                <th>Proyecto</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $item) : ?>
            <tr>
                <td><a href="/admin/contracts/preview/<?php echo $item->id ?>">[Ver]</a></td>
                <td><?php echo $item->number ?></td>
                <td><?php echo $item->project ?></td>
                <td><?php 
                    if (!$item->status_owner) {
                        echo  'Pendiente del impulsor';
                    } elseif (!$item->status_admin) {
                        echo  'Rellenado por el impulsor, Pendiente del admin';
                    } elseif (!$item->status_pdf) {
                        echo  'Verificado por el admin, pendiente de generar pdf';
                    }
                ?></td>
                <td><a href="/admin/contracts/edit/<?php echo $item->id ?>">[Editar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay ningun proyecto con contrato en curso.</p>
<?php endif;?>
</div>