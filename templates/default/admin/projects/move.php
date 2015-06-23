<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>

    <form method="post" action="/admin/projects/move/<?= $this->project->id ?>" >
    <p>
        <label for="node-filter">Pasarselo al nodo:</label><br />
        <select id="node-filter" name="node" >
        <?php foreach ($this->nodes as $nodeId=>$nodeName) : ?>
            <option value="<?php echo $nodeId; ?>"<?php if ($this->project->node == $nodeId) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
        <?php endforeach; ?>
        </select>
    </p>

        <input type="submit" name="save" value="Aplicar" />
    </form>

<?php $this->replace() ?>
