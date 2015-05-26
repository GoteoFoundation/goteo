<?php

use Goteo\Library\Text,
    Goteo\Library\Mail,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout,
    Goteo\Core\View;

$filters = $this['filters'];
$templates = $this['templates'];
$the_filters = '';
foreach ($filters as $key => $value) {
    $the_filters .= "&{$key}={$value}";
}

$pagedResults = new Paginated($this['sended'], 20, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<div class="widget board">
    <form id="filter-form" action="/admin/sended" method="get">
        <div style="float:left;margin:5px;">
            <label for="user-filter">ID, nombre o email del destinatario</label><br />
            <input id="user-filter" name="user" value="<?php echo $filters['user']; ?>" style="width:300px;"/>
        </div>

        <div style="float:left;margin:5px;">
            <label for="template-filter">Plantilla</label><br />
            <select id="template-filter" name="template" onchange="document.getElementById('filter-form').submit();" >
                <option value="">Todas las plantillas</option>
                <?php foreach ($templates as $templateId => $templateName) : ?>
                    <option value="<?php echo $templateId; ?>"<?php if ($filters['template'] == $templateId)
                    echo ' selected="selected"'; ?>><?php echo $templateName; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br clear="both" />

<?php if (!isset($_SESSION['admin_node'])) : ?>
            <div style="float:left;margin:5px;">
                <label for="node-filter">Enviado por el nodo:</label><br />
                <select id="node-filter" name="node" onchange="document.getElementById('filter-form').submit();">
                    <option value="">Cualquier nodo</option>
                    <?php foreach ($this['nodes'] as $nodeId => $nodeName) : ?>
                        <option value="<?php echo $nodeId; ?>"<?php if ($filters['node'] == $nodeId)
                    echo ' selected="selected"'; ?>><?php echo $nodeName; ?></option>
            <?php endforeach; ?>
                </select>
            </div>
<?php endif; ?>

        <div style="float:left;margin:5px;" id="date-filter-from">
            <label for="date-filter-from">Fecha desde</label><br />
<?php echo View::get('superform/element/datebox.html.php', array('value' => $filters['date_from'], 'id' => 'date-filter-from', 'name' => 'date_from', 'js' => true)); ?>
        </div>
        <div style="float:left;margin:5px;" id="date-filter-until">
            <label for="date-filter-until">Fecha hasta</label><br />
<?php echo View::get('superform/element/datebox.html.php', array('value' => $filters['date_until'], 'id' => 'date-filter-until', 'name' => 'date_until', 'js' => true)); ?>
        </div>
        <div style="float:left;margin:5px;">
            <input type="submit" name="filter" value="Filtrar">
        </div>

    </form>
</div>

<div class="widget board">
    <?php if ($filters['filtered'] != 'yes') : ?>
        <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (!empty($this['sended'])) : ?>
        <table>
            <thead>
                <tr>
                    <th width="5%"><!-- Si no ves --></th>
                    <th width="45%">Destinatario</th>
                    <th width="35%">Plantilla</th>
                    <th width="15%">Fecha</th>
                    <th><!-- reenviar --></th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($send = $pagedResults->fetchPagedRow()) :
                    $link = Mail::getSinovesLink($send->id);
                    ?>
                    <tr>
                        <td><a href="<?php echo $link; ?>" target="_blank">[Enlace]</a></td>
                        <td><a href="/admin/users/?name=<?php echo urlencode($send->email) ?>"><?php echo $send->email; ?></a></td>
                        <td><?php echo $templates[$send->template]; ?></td>
                        <td><?php echo $send->date; ?></td>
                        <td><!-- <a href="#" target="_blank">[Reenviar]</a> --></td>
                    </tr>
    <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <ul id="pagination">
    <?php $pagedResults->setLayout(new DoubleBarLayout());
    echo $pagedResults->fetchPagedNavigation(str_replace('?', '&', $the_filters)); ?>
    </ul>
<?php else : ?>
    <p>No se han encontrado registros</p>
<?php endif; ?>
