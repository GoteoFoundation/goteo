<?php

use Goteo\Library\Text;

$task = $vars['task'];
$nodes = $vars['nodes'];
?>
<div class="widget">
    <form action="/admin/tasks/<?php echo ($vars['action'] == 'add') ? 'add' : 'edit/'.$task->id ?>" method="post">
        <?php if (!isset($_SESSION['admin_node']) && $_SESSION['admin_node'] != \GOTEO_NODE) : ?>
        <p>
            <label for="task-node">Nodo:</label><br />
            <select id="task-node" name="node" >
            <?php foreach ($vars['nodes'] as $nodeId=>$nodeName) : ?>
                <option value="<?php echo $nodeId; ?>"<?php if ($task->node == $nodeId) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
            <?php endforeach; ?>
            </select>
        </p>
        <?php else : ?>
        <input type="hidden" name="node" value="<?php echo $_SESSION['admin_node'] ?>" />
        <?php endif; ?>
        <p>
            <label for="task-text">Explicación:</label><br />
            <textarea id="task-text" name="text" style="width:500px;height:200px;" ><?php echo $task->text ?></textarea>
        </p>
        <p>
            <label for="task-url">Url:</label><br />
            <input type="text" id="task-url" name="url" value="<?php echo $task->url ?>" style="width:500px" />
        </p>

        <p>
            <label>Estado:</label><br />
            <?php if (empty($task->done)) : ?>
            <span style="color:red;" >PENDIENTE</span>
            <?php else : ?>
            <span style="color:green;" >Realizada por:</span> <strong><?php echo $task->user->name; ?></strong><br />
            <label><input type="checkbox" name="undone" value="1" />Reabrirla</label>
            <?php endif; ?>
        </p>

        <input type="submit" name="save" value="Guardar" /><br />

    </form>
</div>
