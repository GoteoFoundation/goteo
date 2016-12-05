<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$filters = $this->filters;

?>
<div class="widget board">
    <form id="filter-form" action="/admin/templates" method="get">
        <table>
            <tr>
                <td>
                    <label for="group-filter">Filtrar agrupaci&oacute;n:</label><br />
                    <select id="group-filter" name="group">
                        <option value="">Todas las agrupaciones</option>
                    <?php foreach ($this->groups as $groupId=>$groupName) : ?>
                        <option value="<?= $groupId ?>"<?php if ($filters['group'] == $groupId) echo ' selected="selected"';?>><?= $groupName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <td>
                    <label for="name-filter">Filtrar por nombre o asunto:</label><br />
                    <input type="text" id ="name-filter" name="name" value ="<?= $filters['name']?>" />
                </td>
                <td>
                    <label for="id-filter">Filtrar por id:</label><br />
                    <input type="text" id ="id-filter" name="id" value ="<?= $filters['id']?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="filter" value="Filtrar">
                </td>
            </tr>
        </table>
    </form>
</div>

<div class="widget board">
    <?php if ($this->templates) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th><?= $this->text('regular-template') ?></th>
                <th><?= $this->text('regular-description') ?></th>
                <th><!-- traducir --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->templates as $template) : ?>
            <tr>
                <td><a target="_blank" href="/admin/templates/edit/<?= $template->id ?>">[<?= $this->text('regular-edit') ?>]</a></td>
                <td><?= $template->name ?></td>
                <td><?= $template->purpose ?></td>
                <?php if ($this->has_role('translator')) : ?>
                <td><a target="_blank" href="/translate/template/edit/<?= $template->id ?>" >[<?= $this->text('regular-translate') ?>]</a></td>
                <?php endif ?>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif ?>
</div>


<?php $this->replace() ?>
