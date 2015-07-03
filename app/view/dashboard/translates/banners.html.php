<?php
use Goteo\Model\Banner;

$node = $vars['node'];

$banners = Banner::getList($node->id);
?>
<div class="widget board">
    <?php if (!empty($banners)) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Título</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($banners as $banner) : ?>
            <tr>
                <td><a href="/translate/banner/edit/<?php echo $banner->id; ?>">[Translate]</a></td>
                <td><?php echo $banner->title; ?></td>
                <td><?php echo $banner->description; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
