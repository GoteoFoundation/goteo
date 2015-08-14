<?php

$filters = $this->filters;

?>
<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<div class="widget board">
    <form id="filter-form" action="/admin/mailing/edit" method="get">

        <table>
            <tr>
                <td>
                    <label for="type-filter">A los</label><br />
                    <select id="type-filter" name="type">
                    <?php foreach ($this->types as $typeId => $typeName) : ?>
                        <option value="<?= $typeId ?>"<?php if ($filters['type'] == $typeId) echo ' selected="selected"';?>><?= $typeName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <td>
                    <label for="project-filter">De proyectos que el nombre contenga</label><br />
                    <input id="project-filter" name="project" value="<?= $filters['project']?>" style="width:300px;" />
                </td>
                <td>
                    <label for="status-filter">En estado</label><br />
                    <select id="status-filter" name="status">
                        <option value="-1"<?php if ($filters['status'] == -1) echo ' selected="selected"';?>>Cualquier estado</option>
                    <?php foreach ($this->status as $statusId => $statusName) : ?>
                        <option value="<?= $statusId ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?= $statusName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <td>
                    <label for="method-filter">Aportado mediante</label><br />
                    <select id="method-filter" name="method">
                        <option value="">Cualquier metodo</option>
                    <?php foreach ($this->methods as $methodId => $methodName) : ?>
                        <option value="<?= $methodId ?>"<?php if ($filters['methods'] == $methodId) echo ' selected="selected"';?>><?= $methodName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="interest-filter">Interesados en fin</label><br />
                    <select id="interest-filter" name="interest">
                        <option value="">Cualquiera</option>
                        <option value="15"<?php if ($filters['interest'] == 15) echo ' selected="selected"';?>>__PRUEBAS__</option>
                    <?php foreach ($this->interests as $interestId => $interestName) : ?>
                        <option value="<?= $interestId ?>"<?php if ($filters['interest'] == $interestId) echo ' selected="selected"';?>><?= $interestName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <td>
                    <label for="name-filter">Que el nombre o email contenga</label><br />
                    <input id="name-filter" name="name" value="<?= $filters['name']?>" style="width:300px;" />
                </td>
                <td>
                    <label for="role-filter">Que sean</label><br />
                    <select id="role-filter" name="role">
                        <option value="">Cualquiera</option>
                    <?php foreach ($this->roles as $roleId => $roleName) : ?>
                        <option value="<?= $roleId ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?= $roleName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <td>
                    <label for="comlang-filter">Con idioma preferencia</label><br />
                    <select id="comlang-filter" name="comlang">
                        <option value=""></option>
                        <?php foreach ($this->langs as $lang) : ?>
                            <option value="<?= $lang->id ?>"<?php if ($filters['comlang'] == $lang->id) echo ' selected="selected"';?>><?= $lang->short ?></option>
                        <?php endforeach ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="3"><input type="submit" name="select" value="Buscar destinatarios"></td>
            </tr>
        </table>






    </form>
</div>

<?php $this->replace() ?>
