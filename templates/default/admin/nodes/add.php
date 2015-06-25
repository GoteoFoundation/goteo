<?php

use Goteo\Library\NormalForm;

$node = $this->node;

?>
<?php $this->layout('admin/nodes/layout') ?>

<?php $this->section('admin-node-content') ?>

    <form method="post" action="/admin/nodes" >
        <input type="hidden" name="action" value="add" />
    <p>
        <label for="node-id">Identificador:</label><br />
        http://<input type="text" id="node-id" name="id" value="" />.goteo.org
    </p>
    <p>
        <label for="node-name">Nombre:</label><br />
        <input type="text" id="node-name" name="name" value="" style="width:250px" />
    </p>
    <p>
        <label for="node-email">Email:</label><br />
        <input type="text" id="node-email" name="email" value="" style="width:250px" />
    </p>

    <p>
        <label for="node-sponsors">Límite sponsors:</label><br />
        <input type="text" id="node-sponsors" name="sponsors_limit" value="" />
    </p>

    <p>
        <label for="node-active">Activarlo ahora:</label><br />
        <input type="checkbox" id="node-active" name="active" value="1" />
    </p>

        <input type="submit" name="save" value="Guardar" />
    </form>

<?php $this->replace() ?>
