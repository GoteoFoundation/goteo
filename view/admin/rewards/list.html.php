<?php
use Goteo\Library\Text,
    Goteo\Model\Invest;

$filters = $this['filters'];

$emails = Invest::emails(true);
?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/rewards" method="get">
        <div style="float:left;margin:5px;">
            <label for="projects-filter">Proyecto</label><br />
            <select id="projects-filter" name="projects" onchange="document.getElementById('filter-form').submit();">
                <option value="">Todos los proyectos</option>
            <?php foreach ($this['projects'] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters['projects'] === (string) $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        
        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del usuario:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/accounts/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (!empty($this['list'])) : ?>
    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th>Cofinanciador</th>
                <th>Proyecto</th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Metodo</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $invest) :
                if (in_array($invest->status, array('-1', '2', '4'))) continue;
                ?>
            <tr>
                <td><a href="/admin/rewards/edit/<?php echo $invest->id ?>" >[Gestionar]</a></td>
                <td><a href="/admin/users/manage/<?php echo $invest->user ?>" target="_blank" title="<?php echo $this['users'][$invest->user]; ?>"><?php echo $emails[$invest->user]; ?></a></td>
                <td><a href="/admin/projects/?name=<?php echo $this['projects'][$invest->project] ?>" target="_blank"><?php echo Text::recorta($this['projects'][$invest->project], 20); if (!empty($invest->campaign)) echo '<br />('.$this['calls'][$invest->campaign].')'; ?></a></td>
                <td><?php echo $invest->amount ?></td>
                <td><?php echo $invest->invested ?></td>
                <td><?php echo $this['methods'][$invest->method] ?></td>
                <td><?php echo $this['investStatus'][$invest->investStatus] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay aportes que cumplan con los filtros.</p>
<?php endif;?>
</div>