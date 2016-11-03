<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<a href="/admin/pages/add" class="button">Nueva P&aacute;gina</a>

<div class="widget board">
    <?php if ($this->pages) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Página</th>
                <th>Descripción</th>
                <th><!-- Abrir --></th>
                <th><!-- Traducir --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->pages as $page) : ?>
            <tr>
                <td><a href="/admin/pages/edit/<?= $page->id ?>">[<?= $this->text('regular-edit') ?>]</a></td>
                <td><?= $page->name ?></td>
                <td><?= $page->description ?></td>
                <td><a href="<?= $page->url ?>" target="_blank">[<?= $this->text('regular-view') ?>]</a></td>
                <td>
                <?php if ($this->has_role('translator')) : ?>
                    <a href="/translate/page/edit/<?= $page->id ?>" >[<?= $this->text('regular-translate') ?>]</a>
                <?php endif ?>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif ?>
</div>


<?php $this->replace() ?>
