<?php

use Goteo\Library\Text,
    Goteo\Model\User;

$node = $this['node'];

// administradores
$admins = User::getAdmins(true, $node->admin);

?>
<form method="post" action="/admin/nodes" >
    <input type="hidden" name="action" value="<?php echo $this['action'] ?>" />
    <input type="hidden" name="id" value="<?php echo $node->id; ?>" />

<p>
    <label for="node-name">Nombre:</label><br />
    <input type="text" id="node-name" name="name" value="<?php echo $node->name; ?>" style="width:250px" />
</p>
<p>
    <label for="node-admin">Administrador:</label><br />
    <select id="node-admin" name="admin">
        <option value="">Seleccionar usuario administrador</option>
    <?php foreach ($admins as $userId=>$userName) : ?>
        <option value="<?php echo $userId; ?>"<?php if ($node->admin == $userId) echo' selected="selected"';?>><?php echo $userName; ?></option>
    <?php endforeach; ?>
    </select>
</p>
<p>
    <label for="node-active">Activo:</label><br />
    <input type="checkbox" id="node-active" name="active" value="1" <?php if ($node->active) echo ' checked="checked"'; ?>/>
</p>

    <input type="submit" name="save" value="Guardar" />
</form>
