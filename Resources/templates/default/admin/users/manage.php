<?php $this->layout('admin/users/view_layout') ?>
<?php

$user = $this->user;
$langs = $this->langs;
$location = $this->location;

$url = '/' . $this->template . '/' . $user->id;
?>

<?php $this->section('admin-user-info') ?>

    <tr>
        <td>Estado de la cuenta</td>
        <td>
            <?php if ($user->active) : ?>
                <a href="<?php echo $url . '/ban'; ?>" style="color:green;text-decoration:none;font-weight:bold;">Activa</a>
            <?php else : ?>
                <a href="<?php echo $url . '/unban'; ?>" style="color:red;text-decoration:none;font-weight:bold;">Inactiva</a>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>Visibilidad</td>
        <td>
            <?php if (!$user->hide) : ?>
                <a href="<?php echo $url . '/hide'; ?>" style="color:green;text-decoration:none;font-weight:bold;">Visible</a>
            <?php else : ?>
                <a href="<?php echo $url . '/show'; ?>" style="color:red;text-decoration:none;font-weight:bold;">Oculto</a>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>Crédito en el monedero</td>
        <td>
            <?= (float)$this->poolAmount ?> €
        </td>
    </tr>
    <tr>
        <td>Localización</td>
        <td>
        <?php

            if($location) {
                echo $this->insert('partials/utils/map_canvas', ['latitude' => $location->latitude,
                                                                 'longitude' => $location->longitude,
                                                                 'content' => $user->name."<br>{$user->location}"]);
            } elseif($user->location) {
                echo $this->insert('partials/utils/map_canvas', ['address' => $user->location, 'content' => $user->name."<br>{$user->location}"]);
            }
        ?>
        </td>
    </tr>

<?php $this->append() ?>


<?php $this->section('admin-user-board') ?>

    <?php if (isset($user->roles['translator'])) : ?>

        <h3>Idiomas de traductor</h3>
        <?php if (empty($user->translangs)) : ?><p style="font-weight: bold; color:red;">¡No tiene ningún idioma asignado!</p><?php endif; ?>
        <form method="post" action="<?= $url ?>/translang">
            <input type="hidden" name="user" value="<?php echo $user->id; ?>" />
            <table>
                <?php foreach ($langs as $lang => $name) :
                    $chkckd = (isset($user->translangs[$lang])) ? ' checked="checked"' : '';
                    ?>
                <tr>
                    <td><label><input type="checkbox" name="langs[]" value="<?php echo $lang; ?>"<?php echo $chkckd; ?>/> <?php echo $name; ?></label></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" value="Aplicar">
        </form>
    <?php endif; ?>

    <h3>Roles</h3>
    <form method="post" action="<?= $url ?>">
        <table>
        <thead>
            <tr>
                <th>Role</th>
                <th>Node</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($this->node_roles as $node => $roles): foreach($roles as $role): ?>
            <tr>
                <td><?= $this->all_roles[$role] ?></td>
                <td><?= $this->all_nodes[$node] ?></td>
                <td><a href="<?= $url ?>?<?= http_build_query(array('del_role' => $role, 'from_node' => $node)) ?>">[Eliminar]</a></td>
            </tr>
        <?php endforeach; endforeach ?>

            <tr>
                <td colspan="3"><h4>Añadir rol</h4></td>
            </tr>
            <tr>
                <td><?= $this->html('select', ['options' => $this->new_roles, 'name' => 'add_role']) ?></td>
                <td><?= $this->html('select', ['options' => $this->nodes, 'value' => $this->admin_node, 'name' => 'to_node']) ?></td>
                <td><input type="submit" name="send" value="Add role"></td>
            </tr>
        </tbody>
        </table>
    </form>
<?php $this->append() ?>
