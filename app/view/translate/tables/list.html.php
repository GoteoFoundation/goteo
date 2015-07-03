<?php

use Goteo\Library\Text,
    Goteo\Library\Content,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$bodyClass = 'admin';

$filter = $vars['filter'];
$table  = $vars['table'];
$list  = $vars['list'];
$nwords  = $vars['nwords'];
$types  = $vars['types'];

$pagedResults = new Paginated($list, 20, isset($_GET['page']) ? $_GET['page'] : 1);

// metemos el todos
\array_unshift($types, 'Todos los tipos');
?>
<!-- Filtro -->
<div class="widget board">
    <form id="filter-form" action="/translate/<?php echo $table ?>/list/<?php echo $filter ?>" method="get">
        <input type="hidden" name="table" value="<?php echo $table ?>" />

        <div style="float:left;margin:5px;">
            <label for="filter-type">Filtrar por campo:</label><br />
            <select id="filter-type" name="type" >
            <?php foreach ($types as $val=>$opt) : ?>
                <option value="<?php echo $val; ?>"<?php if ($vars['filters']['type'] == $val) echo ' selected="selected"';?>><?php echo $opt; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="filter-text">Buscar texto:</label><br />
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

<!-- lista -->
<?php if (!empty($list)) : ?>
<div class="widget board">
    N&uacute;mero de palabras: <?php echo $nwords; ?>
</div>

<div class="widget board">
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Texto</th>
                <th>Campo</th>
                <th>Id</th>
                <?php if ($table == 'post') echo '<th></th>'; ?>
            </tr>
        </thead>

        <tbody>
        <?php while ($item = $pagedResults->fetchPagedRow()) : ?>
            <tr>
                <td width="5%"><a title="Registro <?php echo $item->id ?>" href='/translate/<?php echo $table ?>/edit/<?php echo $item->id . $filter . '&page=' . $_GET['page'] ?>' <?php if ($item->pendiente == 1) echo 'style="color:red;"'; ?>>[Translate]</a></td>
                <td width="75%"><?php if ($item->pendiente == 1) echo '* '; echo Text::recorta($item->value, 250) ?></td>
                <td><?php echo $item->fieldName ?></td>
                <td><?php echo $item->id ?></td>
                <?php if ($table == 'post') : ?>
                <td><a href="/blog/<?php echo $item->id; ?>?preview=<?php echo $_SESSION['user']->id ?>" target="_blank">[Ver]</a></td>
                <?php endif; ?>
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
