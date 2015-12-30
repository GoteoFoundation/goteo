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
                        <option value="<?= $roleId ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?= $roleName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <td><?php if ($this->admin_nodes): ?>
                    <label for="node-filter">Del nodo:</label><br />
                    <select id="node-filter" name="node" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier nodo</option>
                    <?php foreach ($this->admin_nodes as $nodeId => $nodeName) : ?>
                        <option value="<?= $nodeId ?>"<?php if ($filters['node'] == $nodeId) echo ' selected="selected"';?>><?= $nodeName ?></option>
                    <?php endforeach ?>
                    </select>
                <?php endif ?></td>
                <td colspan="2">
                    <label for="project-filter">Que aportaron al proyecto:</label><br />
                    <select id="project-filter" name="project" onchange="document.getElementById('filter-form').submit();">
                        <option value="">--</option>
                        <option value="any"<?php if ($filters['project'] == 'any') echo ' selected="selected"';?>>Algún proyecto</option>
                    <?php foreach ($this->projects as $projId=>$projName) : ?>
                        <option value="<?= $projId ?>"<?php if ($filters['project'] == $projId) echo ' selected="selected"';?>><?= substr($projName, 0, 35) ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="id-filter">Id (exacto):</label><br />
                    <input id="id-filter" name="id" value="<?= $filters['id'] ?>" />
                </td>
                <td>
                    <label for="name-filter">Alias/Email:</label><br />
                    <input id="name-filter" name="global" value="<?= $filters['global'] ?>" />
                </td>
                <td>
                    <label for="type-filter">Del  tipo:</label><br />
                    <select id="type-filter" name="type" onchange="document.getElementById('filter-form').submit();">
                        <option value="">--</option>
                    <?php foreach ($this->types as $type=>$desc) : ?>
                        <option value="<?= $type ?>"<?php if ($filters['type'] == $type) echo ' selected="selected"';?>><?= $desc ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <td>
                    <label for="interest-filter">Interesados en:</label><br />
                    <select id="interest-filter" name="interest" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier interés</option>
                    <?php foreach ($this->interests as $interestId=>$interestName) : ?>
                        <option value="<?= $interestId ?>"<?php if ($filters['interest'] == $interestId) echo ' selected="selected"';?>><?= $interestName ?></option>
                    <?php endforeach ?>
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
                        <option value="<?= $orderId ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?= $orderName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
            </tr>
        </table>

    </form>
    <br clear="both" />
    <a href="/admin/users?reset=filters">Quitar filtros</a>
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
                <td><a href="/user/profile/<?= $user->id ?>" target="_blank" <?= $role_node_info ? 'style="color: green;" title="'.implode(' ', $role_node_info).'"' : 'title="Ver perfil público"' ?>><?= substr($user->name, 0, 20) ?></a></td>
                <td><strong><?= substr($user->id, 0, 20) ?></strong></td>
                <td><a href="mailto:<?= $user->email ?>"><?= $user->email ?></a></td>
                <td><?= (isset($user->num_owned)) ? $user->num_owned : $user->get_numOwned ?></td>
                <td><?= (isset($user->num_invested)) ? $user->num_invested : $user->get_numInvested ?></td>
                <td><?= (isset($user->amount)) ? \euro_format($user->amount) : \euro_format($user->get_amount) ?> &euro;</td>
                <td><?= \date_formater($user->created) ?></td>
            </tr>
            <tr>
                <td><a href="/admin/users/manage/<?= $user->id ?>" title="Gestionar">[Gestionar]</a></td>
                <td><?php if ($user->num_invested > 0) {
                    if ($this->is_module_admin('Accounts')) : ?>
                <a href="/admin/accounts?name=<?= $user->email ?>" title="Ver sus aportes">[Aportes]</a>
                <?php else:  ?>
                <a href="/admin/invests?name=<?= $user->email ?>" title="Ver sus aportes">[Aportes]</a>
                <?php endif; } ?></td>
                <td colspan="5" style="color:blue;">
                    <?= (!$user->active && $user->hide) ? ' Baja ' : '' ?>
                    <?= $user->active ? '' : ' Inactivo ' ?>
                    <?= $user->hide ? ' Oculto ' : '' ?>
                    <?= $user->checker ? ' Revisor ' : '' ?>
                    <?= $user->translator ? ' Traductor ' : '' ?>
                    <?= $user->caller ? ' Convocador ' : '' ?>
                    <?= $user->admin ? ' Admin ' : '' ?>
                    <?= $user->manager ? ' Gestor ' : '' ?>
                    <?= $user->vip ? ' VIP ' : '' ?>
                </td>
            </tr>
            <tr>
                <td colspan="7"><hr /></td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>

</div>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>


<?php $this->replace() ?>
