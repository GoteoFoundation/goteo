<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection;

$user = $vars['user'];

if (!$user instanceof Model\user) {
    throw new Redirection('/admin/users');
}

if (empty($user->node)) {
    $user->node = \GOTEO_NODE;
}

?>
<div class="widget" >
    <form method="post" action="/admin/users/move/<?php echo $user->id ?>" >

    <p>
        <label for="node-filter">Pasarselo al nodo:</label><br />
        <select id="node-filter" name="node" >
        <?php foreach ($vars['nodes'] as $nodeId=>$nodeName) : ?>
            <option value="<?php echo $nodeId; ?>"<?php if ($user->node == $nodeId) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
        <?php endforeach; ?>
        </select>
    </p>

        <input type="submit" name="save" value="Aplicar" />
    </form>
</div>
