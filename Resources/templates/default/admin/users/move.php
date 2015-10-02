<?php $this->layout('admin/users/view_layout') ?>

<?php $this->section('admin-user-board') ?>

<?php

$user = $this->user;

?>
<div class="widget" >
    <form method="post" action="/admin/users/move/<?php echo $user->id ?>" >

    <p>
        <label for="node-filter">Pasarselo al nodo:</label><br />
        <select id="node-filter" name="node" >
        <?php foreach ($this->admin_nodes as $nodeId => $nodeName) : ?>
            <option value="<?php echo $nodeId; ?>"<?php if ($user->node == $nodeId) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
        <?php endforeach; ?>
        </select>
    </p>

        <input type="submit" name="save" value="Aplicar" />
    </form>
</div>



<?php $this->append() ?>
