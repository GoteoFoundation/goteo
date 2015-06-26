<?php

$node = $this->node;

?>
<?php $this->layout('admin/nodes/layout') ?>

<?php $this->section('admin-node-content') ?>

    <form method="post" action="/admin/nodes/edit/<?= $node->id ?>" >

    <p>
        <label for="node-name">Nombre:</label><br />
        <input type="text" id="node-name" name="name" value="<?= $node->name; ?>" style="width:250px" />
    </p>
    <p>
        <label for="node-email">Email:</label><br />
        <input type="text" id="node-email" name="email" value="<?= $node->email; ?>" style="width:250px" />
    </p>

    <p>
        <label for="node-consultant">Asesor por defecto:</label><br />
        <select id="node-consultant" name="default_consultant">
            <option value="">Sin asesor</option>
            <?php foreach ($this->node_admins as $userId => $userName) : ?>
                <option value="<?= $userId; ?>" <?= ($node->default_consultant == $userId)? "selected" : '' ?>><?= $userName; ?></option>
            <?php endforeach; ?>
        </select>
    </p>

<?php if(!$node->isMasterNode()): ?>
    <p>
        <label for="node-sponsors">Límite sponsors:</label><br />
        <input type="text" id="node-sponsors" name="sponsors_limit" value="<?= (int) $node->sponsors_limit ?>" style="width:250px" />
    </p>

    <p>
        <label for="node-sponsors">URL (dejar vacio para <b>/channel/<?= $node->id ?>)</b>:</label><br />
        <input type="text" id="node-sponsors" name="sponsors_limit" value="<?= $node->url; ?>" style="width:250px" />
    </p>

    <p>
        <label for="node-active">Activo:</label><br />
        <input type="checkbox" id="node-active" name="active" value="1" <?php if ($node->active) echo ' checked="checked"'; ?>/>
    </p>

<?php endif ?>

        <input type="submit" name="save" value="Guardar" />
    </form>

<?php $this->replace() ?>
