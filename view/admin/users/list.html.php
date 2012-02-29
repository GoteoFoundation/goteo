<?php

use Goteo\Library\Text;

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&interest={$filters['interest']}&role={$filters['role']}&order={$filters['order']}";

?>
<a href="/admin/users/add" class="button red">Crear usuario</a>

<div class="widget board">
    <form id="filter-form" action="/admin/users" method="get">

        <table>
            <tr>
                <td>
                    <label for="status-filter">Mostrar por estado:</label><br />
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todos los estados</option>
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="interest-filter">Mostrar usuarios interesados en:</label><br />
                    <select id="interest-filter" name="interest" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier interés</option>
                    <?php foreach ($this['interests'] as $interestId=>$interestName) : ?>
                        <option value="<?php echo $interestId; ?>"<?php if ($filters['interest'] == $interestId) echo ' selected="selected"';?>><?php echo $interestName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="role-filter">Mostrar usuarios con rol:</label><br />
                    <select id="role-filter" name="role" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier rol</option>
                    <?php foreach ($this['roles'] as $roleId=>$roleName) : ?>
                        <option value="<?php echo $roleId; ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?php echo $roleName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="id-filter">Id:</label><br />
                    <input id="id-filter" name="id" value="<?php echo $filters['id']; ?>" />
                </td>
                <td>
                    <label for="name-filter">Alias:</label><br />
                    <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" />
                </td>
                <td>
                    <label for="email-filter">Email:</label><br />
                    <input id="email-filter" name="email" value="<?php echo $filters['email']; ?>" />
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <input type="submit" name="filter" value="Buscar">
                </td>
                <td></td>
                <td>
                    <label for="order-filter">Ver por:</label><br />
                    <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this['orders'] as $orderId=>$orderName) : ?>
                        <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

    </form>
</div>

<div class="widget board">
    <?php if (!empty($this['users'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Alias</th> <!-- view profile -->
                <th>User</th>
                <th>Email</th>
                <th>Alta</th>
                <th>
                    <!-- Inactivo -->
                    <!-- Oculto -->
                    <!-- Revisor -->
                    <!-- Traductor -->
                </th>
                <th></th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['users'] as $user) : ?>
            <tr>
                <td><a href="/admin/users/manage/<?php echo $user->id; ?>">[Gestionar]</a></td>
                <td><a href="/user/<?php echo $user->id; ?>" target="_blank" title="Preview"><?php echo $user->name; ?></a></td>
                <td><strong><?php echo $user->id; ?></strong></td>
                <td><a href="mailto:<?php echo $user->email; ?>"><?php echo $user->email; ?></a></td>
                <td><?php echo $user->register_date; ?></td>
                <td>
                    <?php echo $user->active ? '' : ' Inactivo'; ?>
                    <?php echo $user->hide ? ' Oculto' : ''; ?>
                    <?php echo $user->checker ? ' Revisor' : ''; ?>
                    <?php echo $user->translator ? ' Traductor' : ''; ?>
                    <?php echo $user->caller ? ' Convocador' : ''; ?>
                    <?php echo $user->admin ? ' Admin' : ''; ?>
                    <?php echo $user->vip ? ' VIP' : ''; ?>
                </td>
                <td><a href="/admin/users/edit/<?php echo $user->id; ?>">[Editar]</a></td>
                <td><a href="/admin/users/impersonate/<?php echo $user->id; ?>">[Suplantar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>