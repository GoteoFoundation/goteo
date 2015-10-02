<?php

use Goteo\Library\Text,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$bodyClass = 'admin';

// paginacion
$filter = $vars['filter'];
$groups = $vars['groups'];
$nwords = $vars['nwords'];
$data   = $vars['data'];

// valores de filtro
$pagedResults = new Paginated($data, 20, isset($_GET['page']) ? $_GET['page'] : 1);

// metemos la agrupación 'todos'
\array_unshift($groups, 'Todas las agrupaciones');
?>
<!-- Filtros -->
<div class="widget board">
    <form id="filter-form" action="/translate/texts/list/<?php echo $filter ?>" method="get">
        <div style="float:left;margin:5px;">
            <label for="filter-group">Filtrar por campo:</label><br />
            <select id="filter-group" name="group">
            <?php foreach ($groups as $val=>$opt) : ?>
                <option value="<?php echo $val; ?>"<?php if ($vars['filters']['group'] == $val) echo ' selected="selected"';?>><?php echo $opt; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="filter-text">Texto:</label><br />
            <input id="filter-text" name="text" value="<?php echo (string) $vars['filters']['text']; ?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="filter-pending">Solo pendientes:</label><br />
            <input id="filter-pending" type="checkbox" name="pending" value="1" <?php if ($vars['filters']['pending'] == 1) echo ' checked="checked"'; ?> />
        </div>

        <br clear="both" />
        <input type="submit" name="filter" value="Buscar">
    </form>
</div>

<?php if (!empty($data)) : ?>
<!-- lista -->
<div class="widget board">
    N&uacute;mero de palabras: <?php echo $nwords; ?>
</div>

<div class="widget board">
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Texto</th>
                <th>Agrupación</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
        <?php while ($item = $pagedResults->fetchPagedRow()) : ?>
            <tr>
                <td width="5%"><a title="Registro <?php echo $item->id ?>" href='/translate/texts/edit/<?php echo $item->id . $filter . '&page=' . $_GET['page']?>' <?php if ($item->pendiente == 1) echo 'style="color:red;"'; ?>>[Translate]</a></td>
                <td width="70%"><?php if ($item->pendiente == 1) echo '* '; ?><?php echo $item->text ?></td>
                <td width="25%"><?php echo $groups[$item->group] ?></td>
                <td></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
    <ul id="pagination">
        <?php   $pagedResults->setLayout(new DoubleBarLayout());
                echo $pagedResults->fetchPagedNavigation(str_replace('?', '&', $filter)); ?>
    </ul>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>
