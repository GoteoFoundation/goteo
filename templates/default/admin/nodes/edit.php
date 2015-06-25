<?php

use Goteo\Library\NormalForm;

$node = $this->node;

?>
<?php $this->layout('admin/nodes/layout') ?>

<?php $this->section('admin-node-content') ?>

    <form method="post" action="/admin/nodes" >
        <input type="hidden" name="action" value="<?php echo $this->action ?>" />
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
        <label for="node-consultant">Asesor por defecto:</label><br />
        <select id="node-consultant" name="default_consultant">
            <option value="">Sin asesor</option>
            <?php foreach ($this->node_admins as $userId=>$userName) : ?>
                <option value="<?php echo $userId; ?>" <?= ($node->default_consultant==$userId)? "selected" : '' ?>><?php echo $userName; ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label for="node-sponsors">Límite sponsors:</label><br />
        <input type="text" id="node-sponsors" name="sponsors_limit" value="<?php echo $node->sponsors_limit; ?>" style="width:250px" />
    </p>

    <p>
        <label for="node-active">Activo:</label><br />
        <input type="checkbox" id="node-active" name="active" value="1" <?php if ($node->active) echo ' checked="checked"'; ?>/>
    </p>

        <input type="submit" name="save" value="Guardar" />
    </form>

<?php $this->replace() ?>
