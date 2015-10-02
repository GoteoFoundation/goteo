<?php
use Goteo\Library\Page,
    Goteo\Model\Node;

$filter = $vars['filter'];
$pages = $vars['pages'];
$nwords = $vars['nwords'];

?>
<!-- Filtros -->
<div class="widget board">
    <form id="filter-form" action="/translate/pages/list/<?php echo $filter ?>" method="get">
        <div style="float:left;margin:5px;">
            <label for="filter-text">Texto:</label><br />
            <input id="filter-text" name="text" value="<?php echo (string) $vars['filters']['text']; ?>" style="width: 500px;"/>
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
<?php if (!empty($pages)) : ?>
<div class="widget board">
    N&uacute;mero de palabras: <?php echo $nwords; ?>
</div>

<div class="widget board">
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Página</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page) : ?>
            <tr>
                <td width="5%"><a title="Registro <?php echo $page->id ?>" href='/translate/pages/edit/<?php echo $page->id . $filter ?>' <?php if ($page->pendiente == 1) echo 'style="color:red;"'; ?>>[Translate]</a></td>
                <td><?php if ($page->pendiente == 1) echo '* '; echo $page->name; ?></td>
                <td><?php echo $page->description; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else : ?>
    <p>No se han encontrado registros</p>
<?php endif; ?>
