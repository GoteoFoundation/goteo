<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<a href="/admin/sphere/add" class="button">Nuevo ámbito</a>

<div class="widget board">
    <?php if ($this->spheres) : ?>
    <table>
        <thead>
            <tr>
                <th>Nombre</th> 
                <th><!-- Edit--></th>
                <th><!-- Delete--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->spheres as $sphere) : ?>
            <tr>
                <td><?= $sphere->name ?></td>
                <td><a href="/admin/sphere/edit/<?= $sphere->id ?>">[Editar]</a></td>
                <td><a href="/admin/sphere/remove/<?= $sphere->id ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Eliminar]</a></td>
                <td><a href="/translate/sphere/edit/<?= $sphere->id ?>" target="_blank">[Traducir]</a></td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
    <?php else : ?>
        <p>No se han encontrado registros</p>
    <?php endif ?>
</div>


<?php $this->replace() ?>
