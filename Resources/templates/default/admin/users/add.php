<?php $this->layout('admin/layout') ?>


<?php $this->section('admin-content') ?>

<?php

$data = $this->data;
?>
<div class="widget">
    <form action="/admin/users/add" method="post">
        <p>
            <label for="user-user">Login:</label><span style="font-style:italic;">Solo letras, números y guion '-'. Sin espacios ni tildes ni 'ñ' ni 'ç' ni otros caracteres que no sean letra, número o guión.</span><br />
            <input type="text" id="user-user" name="userid" value="<?= isset($data['user']) ? $data['user'] : '' ?>" style="width:250px" maxlength="50"/>
        </p>
        <p>
            <label for="user-name">Nombre público:</label><br />
            <input type="text" id="user-name" name="name" value=<?=  isset($data['name']) ? $data['name'] : '' ?>"" style="width:500px" maxlength="255"/>
        </p>
        <p>
            <label for="user-email">Email:</label><span style="font-style:italic;">Que sea válido.</span><br />
            <input type="text" id="user-email" name="email" value="<?= isset($data['email']) ? $data['email'] : '' ?>" style="width:500px" maxlength="255"/>
        </p>
        <p>
            <label for="user-password">Contraseña:</label><span style="font-style:italic;">Mínimo 6 caracteres. Se va a encriptar y no se puede consultar</span><br />
            <input type="text" id="user-password" name="password" value="<?= isset($data['password']) ? $data['password'] : '' ?>" style="width:500px" maxlength="255"/>
        </p>

    <p>
        <label for="user-node">Nodo:</label><br />
        <select id="user-node" name="node" >
        <?php foreach ($this->admin_nodes as $nodeId => $nodeName) : ?>
            <option value="<?php echo $nodeId; ?>"<?php if ($nodeId == $this->admin_node) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
        <?php endforeach; ?>
        </select>
    </p>

        <input type="submit" name="add" value="Crear este usuario" /><br />
        <span style="font-style:italic;font-weight:bold;">Atención! Le llegará email de verificación al usuario como si se hubiera registrado.</span>

    </form>
</div>

<?php $this->replace() ?>

