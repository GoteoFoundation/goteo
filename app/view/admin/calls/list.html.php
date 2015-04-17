<?php

use Goteo\Library\Text;

$filters = $vars['filters'];

?>
<?php if (isset($_SESSION['user']->roles['superadmin'])) : ?>
<a href="/admin/calls/add" class="button">Crear convocatoria</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/transcalls" class="button">Asignar traductores</a>
<?php endif; ?>
<div class="widget board">
    <form id="filter-form" action="/admin/calls" method="get">
        <table>
            <tr>
                <td>
                    <label for="caller-filter">Del Convocador:</label><br />
                    <select id="caller-filter" name="caller" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier convocador</option>
                    <?php foreach ($vars['callers'] as $callerId=>$callerName) : ?>
                        <option value="<?php echo $callerId; ?>"<?php if ($filters['caller'] == $callerId) echo ' selected="selected"';?>><?php echo $callerName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="category-filter">De la categoría:</label><br />
                    <select id="category-filter" name="category" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier categoría</option>
                    <?php foreach ($vars['categories'] as $categoryId=>$categoryName) : ?>
                        <option value="<?php echo $categoryId; ?>"<?php if ($filters['category'] == $categoryId) echo ' selected="selected"';?>><?php echo $categoryName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <?php if (!isset($_SESSION['user']->roles['admin'])) : ?>
                <td>
                    <label for="admin-filter">Administradas por:</label><br />
                    <select id="admin-filter" name="admin" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier administrador</option>
                    <?php foreach ($vars['admins'] as $userId=>$userName) : ?>
                        <option value="<?php echo $userId; ?>"<?php if ($filters['admin'] == $userId) echo ' selected="selected"';?>><?php echo $userName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>
                    <label for="name-filter">Nombre:</label><br />
                    <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" style="width:250px"/>
                </td>
                <td>
                    <label for="status-filter">Mostrar por estado:</label><br />
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todos los estados</option>
                    <?php foreach ($vars['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="order-filter">Ver por:</label><br />
                    <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($vars['orders'] as $orderId=>$orderName) : ?>
                        <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="filter" value="Buscar">
                </td>
            </tr>
        </table>
    </form>
</div>

<?php if (!empty($vars['calls'])) : ?>
    <?php foreach ($vars['calls'] as $call) : ?>
    <div class="widget board">
        <table>
            <thead>
                <tr>
                    <th>Convocatoria</th> <!-- edit -->
                    <th>Creador</th> <!-- mailto -->
                    <th>Apertura aplicacion</th> <!-- aplicacion de proyectos -->
                    <th>Estado</th>
                    <th>Presupuesto</th>
                    <th>Restante</th>
                    <th>Asignados</th>
                </tr>
            </thead>

            <tbody>
            <tr>
                <td><a href="/call/<?php echo $call->id; ?>" target="_blank" title="Preview"><?php echo $call->name; ?></a></td>
                <td><?php echo $call->user->name; ?></td>
                <td><?php if (!empty($call->opened)) echo date('d-m-Y', strtotime($call->opened)); ?></td>
                <td><?php echo $vars['status'][$call->status]; ?></td>
                <td><?php echo $call->amount; ?></td>
                <td><?php echo $call->rest; ?></td>
                <td><?php echo $call->num_projects; ?></td>
            </tr>
            <tr>
                <td colspan="7"> GESTI&Oacute;N:&nbsp;
                    <a href="/call/edit/<?php echo $call->id; ?>" target="_blank">[Editar]</a>
                    <a href="/admin/users/?id=<?php echo $call->owner; ?>" target="_blank">[Convocador]</a>
                    <a href="<?php echo "/admin/calls/projects/{$call->id}"; ?>">[Proyectos]</a>
                    <?php if (isset($_SESSION['user']->roles['superadmin'])) : ?><a href="<?php echo "/admin/calls/admins/{$call->id}"; ?>">[Administradores]</a><?php endif; ?>
                    <a href="<?php echo "/admin/calls/conf/{$call->id}"; ?>">[Configuraci&oacute;n]</a>
                    <a href="<?php echo "/admin/calls/dropconf/{$call->id}"; ?>">[Configuraci&oacute;n Económica]</a>
                    <?php if (isset($_SESSION['user']->roles['superadmin']) && $call->status == 1) : ?>
                    &nbsp;|&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo "/admin/calls/delete/{$call->id}"; ?>" onclick="return confirm('La convocatoria va a ELIMINAR comlpetamente, ¿seguro que hacemos eso?');" style="color: red;">[Suprimir]</a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    CONTENIDOS:&nbsp;
                    <a href="<?php echo "/admin/calls/posts/{$call->id}"; ?>">[Posts]</a>
                    &nbsp;|&nbsp;&nbsp;&nbsp;
                    <?php if ($call->translate) : ?><a href="<?php echo "/admin/transcalls/edit/{$call->id}"; ?>">[Asignar traducción]</a>
                    <?php else : ?><a href="<?php echo "/admin/transcalls/add/?call={$call->id}"; ?>">[Habilitar traducción]</a><?php endif; ?>
                    <?php if (isset($_SESSION['user']->roles['translator'])) : ?><a href="<?php echo "/dashboard/translates"; ?>" target="_blank">[Abrir Mis Traducciones]</a><?php endif; ?>
                </td>
            </tr>
            <?php if (($call->status != 5) && ($call->status != 6)) { ?>
            <tr>
                <td colspan="6">
                    PROCESO:&nbsp;
                    <?php if ($call->status == 1) : ?><a href="<?php echo "/admin/calls/review/{$call->id}"; ?>">[A revisión]</a><?php endif; ?>
                    <?php if ($call->status < 3) : ?><a href="<?php echo "/admin/calls/open/{$call->id}"; ?>" onclick="return confirm('La convocatoria va a comenzar a recibir la inscripción de proyectos, ok?');">[Abrir aplicacion]</a><?php endif; ?>
                    <?php if ($call->status < 4) : ?><a href="<?php echo "/admin/calls/publish/{$call->id}"; ?>" onclick="return confirm('La convocatoria va a comenzar a repartir dinero a los proyectos seleccionados, ok?');">[Publicar]</a><?php endif; ?>
                    <?php if ($call->status > 1 && $call->status < 4) : ?><a href="<?php echo "/admin/calls/enable/{$call->id}"; ?>" onclick="return confirm('Ojo si la convocatoria está publicandose ahora mismo... ¿seguimos?');">[Reabrir edición]</a><?php endif; ?>
                    <?php if ($call->status == 4) : ?><a href="<?php echo "/admin/calls/complete/{$call->id}"; ?>" onclick="return confirm('Significa que no se repartirá más dinero, ok?');">[Completar]</a><?php endif; ?>
                    <?php if ($call->status == 4) : ?><a href="<?php echo "/admin/calls/cancel/{$call->id}"; ?>" onclick="return confirm('La convocatoria se va a CANCELAR (no completada), ¿seguro que hacemos eso?');">[Cancelar]</a><?php endif; ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
    <?php endforeach; ?>
<?php else : ?>
    <p>No se han encontrado registros</p>
<?php endif; ?>
