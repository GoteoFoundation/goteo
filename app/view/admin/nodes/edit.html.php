<?php

use Goteo\Library\Text,
    Goteo\Model\User;

$node = $vars['node'];

?>
<form method="post" action="/admin/nodes" >
    <input type="hidden" name="action" value="<?php echo $vars['action'] ?>" />
    <input type="hidden" name="url" value="<?php echo $node->url; ?>" />
    <input type="hidden" name="id" value="<?php echo $node->id; ?>" />

<p>
    <label for="node-name">Nombre:</label><br />
    <input type="text" id="node-name" name="name" value="<?php echo $node->name; ?>" style="width:250px" />
</p>
<p>
    <label for="node-email">Email:</label><br />
    <input type="text" id="node-email" name="email" value="<?php echo $node->email; ?>" style="width:250px" />
</p>
<p>
    <label for="node-active">Activo:</label><br />
    <input type="checkbox" id="node-active" name="active" value="1" <?php if ($node->active) echo ' checked="checked"'; ?>/>
</p>

    <input type="submit" name="save" value="Guardar" />
</form>
