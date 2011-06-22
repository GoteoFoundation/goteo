<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&interest={$filters['interest']}";

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Administración de usuarios</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

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
                </form>
            </div>

            <div class="widget board">
                <?php if (!empty($this['users'])) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th> <!-- view profile -->
                            <th>Email</th>
                            <th>Estado</th>
                            <th></th>
                            <th>Revisor</th>
                            <th></th>
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
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
                <?php else : ?>
                <p>No se han encontrado registros</p>
                <?php endif; ?>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';