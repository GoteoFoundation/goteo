<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>
<?php

$node = $this->node;

$transNode = \Goteo\Model\User\Translate::is_legal($this->get_user()->id, $node, 'node') ? true : false;
$translator = $this->has_role('translator', $node) ? true : false;

?>
<a href="/admin/milestones/add" class="button">Nuevo hito</a>
<?php if ($node && !$this->is_master_node($node)) : ?>
<a href="/translate/banner/list" class="button">Traducir hitos</a>
<?php endif ?>

<div class="widget board">
    <?php if ($this->milestones) : ?>
    <table>
        <thead>
            <tr>
                <th>Descripcion</th>
                <th>Tipo</th>
                <th><!-- Editar--></th>
                <th><!-- Traducir--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->milestones as $milestone) :
                ?>
            <tr>
                <td><?= $milestone->description ?></td>
                <td><?= $this->types[$milestone->type] ?></td>
                <td><a href="/admin/milestones/edit/<?= $milestone->id ?>">[Editar]</a></td>
                <td>
                <?php if ($transNode || $translator) : ?>
                <a href="/translate/milestone/edit/<?= $milestone->id ?>" target="_blank">[Traducir]</a>
                <?php endif ?>
                </td>
                <td><a href="/admin/milestones/remove/<?= $milestone->id ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif ?>
</div>

<?php $this->replace() ?>
