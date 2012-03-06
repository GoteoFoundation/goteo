<?php

use Goteo\Library\Text;

$filters = $this['filters'];

?>
<a href="/admin/calls/add" class="button red">Crear convocatoria</a>

<div class="widget board">
    <form id="filter-form" action="/admin/calls" method="get">
        <table>
            <tr>
                <td>
                    <label for="caller-filter">Del Convocador:</label><br />
                    <select id="caller-filter" name="caller" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier convocador</option>
                    <?php foreach ($this['callers'] as $callerId=>$callerName) : ?>
                        <option value="<?php echo $callerId; ?>"<?php if ($filters['caller'] == $callerId) echo ' selected="selected"';?>><?php echo $callerName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="category-filter">De la categoría:</label><br />
                    <select id="category-filter" name="category" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier categoría</option>
                    <?php foreach ($this['categories'] as $categoryId=>$categoryName) : ?>
                        <option value="<?php echo $categoryId; ?>"<?php if ($filters['category'] == $categoryId) echo ' selected="selected"';?>><?php echo $categoryName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
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
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="order-filter">Ver por:</label><br />
                    <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this['orders'] as $orderId=>$orderName) : ?>
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

<div class="widget board">
    <?php if (!empty($this['calls'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Convocatoria</th> <!-- edit -->
                <th>Creador</th> <!-- mailto -->
                <th>Apertura aplicacion</th> <!-- aplicacion de proyuectos -->
                <th>Estado</th>
                <th>Presupuesto</th>
                <th>Restante</th>
                <th>Asignados</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['calls'] as $call) : ?>
            <tr>
                <td><a href="/call/<?php echo $call->id; ?>" target="_blank" title="Preview"><?php echo $call->name; ?></a></td>
                <td><?php echo $call->user->name; ?></td>
                <td><?php if (!empty($call->opened)) echo date('d-m-Y', strtotime($call->opened)); ?></td>
                <td><?php echo $this['status'][$call->status]; ?></td>
                <td><?php echo $call->amount; ?></td>
                <td><?php echo $call->rest; ?></td>
                <td><?php echo count($call->projects); ?></td>
            </tr>
            <tr>
                <td colspan="6"> >>> Acciones:
                    <a href="/call/edit/<?php echo $call->id; ?>" target="_blank">[Editar]</a>
                    <?php if ($call->status == 1) : ?><a href="<?php echo "/admin/calls/review/{$call->id}"; ?>">[A revisión]</a><?php endif; ?>
                    <?php if ($call->status < 3) : ?><a href="<?php echo "/admin/calls/open/{$call->id}"; ?>" onclick="return confirm('La convocatoria va a comenzar a recibir la inscripción de proyectos, ok?');">[Abrir aplicacion]</a><?php endif; ?>
                    <?php if ($call->status < 4) : ?><a href="<?php echo "/admin/calls/publish/{$call->id}"; ?>" onclick="return confirm('La convocatoria va a comenzar a repartir dinero a los proyectos seleccionados, ok?');">[Publicar]</a><?php endif; ?>
                    <?php if ($call->status > 1) : ?><a href="<?php echo "/admin/calls/enable/{$call->id}"; ?>" onclick="return confirm('Ojo si la convocatoria está publicandose ahora mismo... ¿seguimos?');">[Reabrir edición]</a><?php endif; ?>
                    <a href="<?php echo "/admin/calls/projects/{$call->id}"; ?>">[Proyectos]</a>
                    <?php if ($call->translate) : ?><a href="<?php echo "/admin/translates/calls/{$call->id}"; ?>">[Ir a traducción]</a><?php endif; ?>
                    <a href="<?php echo "/admin/calls/cancel/{$call->id}"; ?>" onclick="return confirm('La convocatoria va a CADUCAR, ¿seguro que hacemos eso?');">[Cancelar]</a>
                    <?php if ($call->status == 1) : ?><a href="<?php echo "/admin/calls/delete/{$call->id}"; ?>" onclick="return confirm('La convocatoria va a ELIMINAR comlpetamente, ¿seguro que hacemos eso?');" style="color: red;">[Suprimir]</a><?php endif; ?>
                    <?php if ($call->translate) : ?><a href="<?php echo "/admin/transcalls/edit/{$call->id}"; ?>">[Ir a traducción]</a>
                    <?php else : ?><a href="<?php echo "/admin/transcalls/add/?call={$call->id}"; ?>">[Habilitar traducción]</a><?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="6"><hr /></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>