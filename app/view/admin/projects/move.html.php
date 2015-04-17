<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\Message;

$project = $vars['project'];

if (!$project instanceof Model\Project) {
    Message::Error('Instancia de proyecto corrupta');
    throw new Redirection('/admin/projects');
}

?>
<div class="widget" >
    <form method="post" action="/admin/projects" >
        <input type="hidden" name="save-node" value="save-node" />
        <input type="hidden" name="id" value="<?php echo $project->id ?>" />

    <p>
        <label for="node-filter">Pasarselo al nodo:</label><br />
        <select id="node-filter" name="node" >
        <?php foreach ($vars['nodes'] as $nodeId=>$nodeName) : ?>
            <option value="<?php echo $nodeId; ?>"<?php if ($project->node == $nodeId) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
        <?php endforeach; ?>
        </select>
    </p>

        <input type="submit" name="save" value="Aplicar" />
    </form>
</div>
