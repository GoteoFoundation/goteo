<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$filters = $this->filters;
$users = $this->users;

// la ordenación por cantidad y proyectos hay que hacerla aqui
if ($filters['order'] == 'amount') {
    uasort($users,
        function ($a, $b) {
            if ($a->namount == $b->namount) return 0;
            return ($a->namount < $b->namount) ? 1 : -1;
            }
        );
}
if ($filters['order'] == 'projects') {
    uasort($users,
        function ($a, $b) {
            if ($a->nprojs == $b->nprojs) return 0;
            return ($a->nprojs < $b->nprojs) ? 1 : -1;
            }
        );
}

$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters .= "&{$key}={$value}";
}

?>
<a href="/admin/users/add" class="button">Crear usuario</a>

<div class="widget board">
    <form id="filter-form" action="/admin/users" method="get">
        <table>
            <tr>
                <td>
                    <label for="role-filter">Con rol:</label><br />
                    <select id="role-filter" name="role" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier rol</option>
                    <?php foreach ($this->roles as $roleId => $roleName) : ?>
                        <option value="<?php echo $roleId; ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?php echo $roleName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td><?php if (count($this->admin_nodes) > 0) : ?>
                    <label for="node-filter">Del nodo:</label><br />
                    <select id="node-filter" name="node" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier nodo</option>
                    <?php foreach ($this->admin_nodes as $nodeId => $nodeName) : ?>
                        <option value="<?php echo $nodeId; ?>"<?php if ($filters['node'] == $nodeId) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
                    <?php endforeach; ?>
                    </select>
                <?php endif; ?></td>
                <td colspan="2">
                    <label for="project-filter">Que aportaron al proyecto:</label><br />
                    <select id="project-filter" name="project" onchange="document.getElementById('filter-form').submit();">
                        <option value="">--</option>
                        <option value="any"<?php if ($filters['project'] == 'any') echo ' selected="selected"';?>>Algún proyecto</option>
                    <?php foreach ($this->projects as $projId=>$projName) : ?>
                        <option value="<?php echo $projId; ?>"<?php if ($filters['project'] == $projId) echo ' selected="selected"';?>><?php echo substr($projName, 0, 35); ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="id-filter">Id (exacto):</label><br />
                    <input id="id-filter" name="id" value="<?php echo $filters['id']; ?>" />
                </td>
                <td>
                    <label for="name-filter">Alias/Email:</label><br />
                    <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" />
                </td>
                <td>
                    <label for="type-filter">Del  tipo:</label><br />
                    <select id="type-filter" name="type" onchange="document.getElementById('filter-form').submit();">
                        <option value="">--</option>
                    <?php foreach ($this->types as $type=>$desc) : ?>
                        <option value="<?php echo $type; ?>"<?php if ($filters['type'] == $type) echo ' selected="selected"';?>><?php echo $desc; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="interest-filter">Interesados en:</label><br />
                    <select id="interest-filter" name="interest" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier interés</option>
                    <?php foreach ($this->interests as $interestId=>$interestName) : ?>
                        <option value="<?php echo $interestId; ?>"<?php if ($filters['interest'] == $interestId) echo ' selected="selected"';?>><?php echo $interestName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="filter" value="Buscar">
                </td>
                <td>
                    <label for="order-filter">Ver por:</label><br />
                    <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this->orders as $orderId=>$orderName) : ?>
                        <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

    </form>
    <br clear="both" />
    <a href="/admin/users/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
    <p><strong><?= $this->total ?></strong> usuarios cumplen este filtro </p>
    <table>
        <thead>
            <tr>
                <th>Alias</th> <!-- view profile -->
                <th>User</th>
                <th>Email</th>
                <th>Impulsados</th>
                <th>Cofinanciados</th>
                <th>Cantidad</th>
                <th>Alta</th>
            </tr>
        </thead>

        <tbody>
            <?php
             foreach ($users as $user) :
                $node_roles = array_intersect_key($user->getAllNodeRoles(), $this->admin_nodes);
                $role_node_info = array();
                foreach($node_roles as $n => $roles) {
                    $role_node_info[] = $n .'[' . implode(' ', $roles) . ']';
                }
                ?>
            <tr>
                <td><a href="/user/profile/<?php echo $user->id; ?>" target="_blank" <?= $role_node_info ? 'style="color: green;" title="'.implode(' ', $role_node_info).'"' : 'title="Ver perfil público"'; ?>><?php echo substr($user->name, 0, 20); ?></a></td>
                <td><strong><?php echo substr($user->id, 0, 20); ?></strong></td>
                <td><a href="mailto:<?php echo $user->email; ?>"><?php echo $user->email; ?></a></td>
                <td><?php echo (isset($user->num_owned)) ? $user->num_owned : $user->get_numOwned; ?></td>
                <td><?php echo (isset($user->num_invested)) ? $user->num_invested : $user->get_numInvested; ?></td>
                <td><?php echo (isset($user->amount)) ? \euro_format($user->amount) : \euro_format($user->get_amount); ?> &euro;</td>
                <td><?php echo $user->register_date; ?></td>
            </tr>
            <tr>
                <td><a href="/admin/users/manage/<?php echo $user->id; ?>" title="Gestionar">[Gestionar]</a></td>
                <td><?php if ($user->num_invested > 0) {
                    if (!isset($_SESSION['admin_node']) || $_SESSION['admin_node'] == \GOTEO_NODE ) : ?>
                <a href="/admin/accounts/?name=<?php echo $user->email; ?>" title="Ver sus aportes">[Aportes]</a>
                <?php else:  ?>
                <a href="/admin/invests/?name=<?php echo $user->email; ?>" title="Ver sus aportes">[Aportes]</a>
                <?php endif; } ?></td>
                <td colspan="5" style="color:blue;">
                    <?php echo (!$user->active && $user->hide) ? ' Baja ' : ''; ?>
                    <?php echo $user->active ? '' : ' Inactivo '; ?>
                    <?php echo $user->hide ? ' Oculto ' : ''; ?>
                    <?php echo $user->checker ? ' Revisor ' : ''; ?>
                    <?php echo $user->translator ? ' Traductor ' : ''; ?>
                    <?php echo $user->caller ? ' Convocador ' : ''; ?>
                    <?php echo $user->admin ? ' Admin ' : ''; ?>
                    <?php echo $user->manager ? ' Gestor ' : ''; ?>
                    <?php echo $user->vip ? ' VIP ' : ''; ?>
                </td>
            </tr>
            <tr>
                <td colspan="7"><hr /></td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>

</div>

<?= $this->insert('partials/utils/paginator', ['page' => 0, 'total' => $this->total, 'limit' => 100]) ?>



<?php $this->replace() ?>
