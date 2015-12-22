<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>
<?php

$node = $this->node;

$transNode = \Goteo\Model\User\Translate::is_legal($this->get_user()->id, $node, 'node') ? true : false;
$translator = $this->has_role('translator', $node) ? true : false;

?>
<a href="/admin/banners/add" class="button">Nuevo banner</a>
<?php if ($node && !$this->is_master_node($node)) : ?>
<a href="/translate/banner/list" class="button">Traducir banners</a>
<?php endif ?>

<div class="widget board">
    <?php if ($this->bannered) : ?>
    <table>
        <thead>
            <tr>
                <th><?= ($this->is_master_node($node)) ? 'Proyecto' : 'Título' ?></th>
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- Traducir--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->bannered as $banner) :
                $banner_title = ($banner->name) ? $banner->name : $banner->title;
                ?>
            <tr>
                <td><?= ($banner->active) ? '<strong>'.$banner_title.'</strong>' : $banner_title ?></td>
                <td><?= $banner->status ?></td>
                <td><?= $banner->order ?></td>
                <td><a href="/admin/banners/up/<?= $banner->id ?>">[&uarr;]</a></td>
                <td><a href="/admin/banners/down/<?= $banner->id ?>">[&darr;]</a></td>
                <td><a href="/admin/banners/edit/<?= $banner->id ?>">[Editar]</a></td>
                <td><?php if ($banner->active) : ?>
                <a href="/admin/banners/active/<?= $banner->id ?>/off">[Ocultar]</a>
                <?php else : ?>
                <a href="/admin/banners/active/<?= $banner->id ?>/on">[Mostrar]</a>
                <?php endif ?></td>
                <td>
                <?php if ($transNode || $translator) : ?>
                <a href="/translate/banner/edit/<?= $banner->id ?>" target="_blank">[Traducir]</a>
                <?php endif ?>
                </td>
                <td><a href="/admin/banners/remove/<?= $banner->id ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif ?>
</div>

<?php $this->replace() ?>
