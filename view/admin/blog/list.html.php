<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

// paginacion
require_once 'library/pagination/pagination.php';

$translator = ACL::check('/translate') ? true : false;
$node = $this['node'];
$transNode = ACL::check('/translate/node/'.$node) ? true : false;

$filters = $this['filters'];
if (empty($filters['show'])) $filters['show'] = 'all';
$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters .= "&{$key}={$value}";
}

$the_posts = array();
foreach ($this['posts'] as $apost) {
    $the_posts[] = $apost;
}
$pagedResults = new \Paginated($the_posts, 10, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<a href="/admin/blog/add" class="button">Nueva entrada</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/blog/reorder" class="button">Ordenar la portada</a>
<?php if ($node == \GOTEO_NODE) : ?>
&nbsp;&nbsp;&nbsp;
<a href="/admin/blog/footer" class="button">Ordenar el footer</a>
<?php endif; ?>

<div class="widget board">
    <form id="filter-form" action="/admin/blog" method="get">
        <div style="float:left;margin:5px;">
            <label for="show-filter">Mostrar:</label><br />
            <select id="shhow-filter" name="show" onchange="document.getElementById('filter-form').submit();">
            <?php foreach ($this['show'] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters['show'] === (string) $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($this['posts'])) : ?>
    <table>
        <thead>
            <tr>
                <th colspan="6">Entrada</th> <!-- title -->
                <th>Fecha</th> <!-- date -->
                <th>Autor</th>
                <th><!-- published --></th>
            </tr>
        </thead>

        <tbody>
            <?php while ($post = $pagedResults->fetchPagedRow()) : ?>
            <tr>
                <td colspan="6"><?php
                        $style = '';
                        if (isset($this['homes'][$post->id]))
                            $style .= ' font-weight:bold;';
                        if (empty($_SESSION['admin_node']) || $_SESSION['admin_node'] == \GOTEO_NODE) {
                            if (isset($this['footers'][$post->id]))
                                $style .= ' font-style:italic;';
                        }
                            
                      echo "<span style=\"{$style}\">{$post->title}</span>";
                ?></td>
                <td><?php echo $post->fecha; ?></td>
                <td><?php echo $post->user->name; ?></td>
                <td><?php if ($post->publish) echo '<strong style="color:#20b2b3;font-size:10px;">Publicada</sttrong>'; ?></td>
            </tr>
            <tr>
                <td><a href="/blog/<?php echo $post->id; ?>?preview=<?php echo $_SESSION['user']->id ?>" target="_blank">[Ver]</a></td>
                <td><?php if ($post->owner == 'node-'.$node || $node == \GOTEO_NODE) : ?>
                    <a href="/admin/blog/edit/<?php echo $post->id; ?>">[Editar]</a>
                <?php endif; ?></td>
                <td><?php if (isset($this['homes'][$post->id])) {
                        echo '<a href="/admin/blog/remove_home/'.$post->id.'" style="color:red;">[Quitar de portada]</a>';
                    } elseif ($post->publish) {
                        echo '<a href="/admin/blog/add_home/'.$post->id.'" style="color:blue;">[Poner en portada]</a>';
                    } ?></td>
                <td><?php if (empty($_SESSION['admin_node']) || $_SESSION['admin_node'] == \GOTEO_NODE) {
                        if (isset($this['footers'][$post->id])) {
                            echo '<a href="/admin/blog/remove_footer/'.$post->id.'" style="color:red;">[Quitar del footer]</a>';
                        } elseif ($post->publish) {
                            echo '<a href="/admin/blog/add_footer/'.$post->id.'" style="color:blue;">[Poner en footer]</a>';
                        }
                    } ?></td>
                <td>
                <?php if ($translator && $node == \GOTEO_NODE) : ?><a href="/translate/post/edit/<?php echo $post->id; ?>" >[Traducir]</a><?php endif; ?>
                <?php if ($post->owner == 'node-'.$node && $transNode && $node != \GOTEO_NODE) : ?><a href="/translate/node/<?php echo $node ?>/post/edit/<?php echo $post->id; ?>" target="_blank">[Traducir]</a><?php endif; ?>
                </td>
                <td><?php if (!$post->publish && ($post->owner == 'node-'.$_SESSION['admin_node'] || !isset($_SESSION['admin_node']))) : ?>
                    <a href="/admin/blog/remove/<?php echo $post->id; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Eliminar]</a>
                <?php endif; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="9"><hr /></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<ul id="pagination" style="margin-bottom: 10px; padding-left: 150px;">
<?php   $pagedResults->setLayout(new DoubleBarLayout());
        echo $pagedResults->fetchPagedNavigation(str_replace('?', '&', $the_filters)); ?>
</ul>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>