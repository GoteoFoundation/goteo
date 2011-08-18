<?php

use Goteo\Library\Text;

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&interest={$filters['interest']}";

?>
<div class="widget board">
    <form id="filter-form" action="/admin/managing" method="get">
        <label for="status-filter">Mostrar por estado:</label>
        <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
            <option value="">Todos los estados</option>
        <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
            <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
        <?php endforeach; ?>
        </select>

        <label for="interest-filter">Mostrar usuarios interesados en:</label>
        <select id="interest-filter" name="interest" onchange="document.getElementById('filter-form').submit();">
            <option value="">Cualquier interés</option>
        <?php foreach ($this['interests'] as $interestId=>$interestName) : ?>
            <option value="<?php echo $interestId; ?>"<?php if ($filters['interest'] == $interestId) echo ' selected="selected"';?>><?php echo $interestName; ?></option>
        <?php endforeach; ?>
        </select>


        <label for="role-filter">Mostrar usuarios con rol:</label>
        <select id="role-filter" name="role" onchange="document.getElementById('filter-form').submit();">
            <option value="">Cualquier rol</option>
        <?php foreach ($this['roles'] as $roleId=>$roleName) : ?>
            <option value="<?php echo $roleId; ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?php echo $roleName; ?></option>
        <?php endforeach; ?>
        </select>

        <br />
        <label for="name-filter">Por nombre o email:</label>
        <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" />
        <input type="submit" name="filter" value="Buscar">

    </form>
</div>

<div class="widget board">
    <?php if (!empty($this['users'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Usuario</th> <!-- view profile -->
                <th>Email</th>
                <th colspan="2">Estado</th>
                <th colspan="2">Revisor</th>
                <th colspan="2">Traductor</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['users'] as $user) : ?>
            <tr>
                <td><a href="/user/<?php echo $user->id; ?>" target="_blank" title="Preview"><?php echo $user->name; ?></a></td>
                <td><?php echo $user->email; ?></td>
                <td><?php echo $user->active ? 'Activo' : 'Inactivo'; ?></td>
                <?php if ($user->active) : ?>
                <td><a href="<?php echo "/admin/managing/ban/{$user->id}{$filter}"; ?>">[Desactivar]</a></td>
                <?php else : ?>
                <td><a href="<?php echo "/admin/managing/unban/{$user->id}{$filter}"; ?>">[Activar]</a></td>
                <?php endif; ?>
                <td><?php echo $user->checker ? 'Revisor' : ''; ?></td>
                <?php if ($user->checker) : ?>
                <td><a href="<?php echo "/admin/managing/nochecker/{$user->id}{$filter}"; ?>">[Quitarlo de revisor]</a></td>
                <?php else : ?>
                <td><a href="<?php echo "/admin/managing/checker/{$user->id}{$filter}"; ?>">[Hacerlo revisor]</a></td>
                <?php endif; ?>
                <td><?php echo $user->translator ? 'Traductor' : ''; ?></td>
                <?php if ($user->translator) : ?>
                <td><a href="<?php echo "/admin/managing/notranslator/{$user->id}{$filter}"; ?>">[Quitarlo de traductor]</a></td>
                <?php else : ?>
                <td><a href="<?php echo "/admin/managing/translator/{$user->id}{$filter}"; ?>">[Hacerlo traductor]</a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>