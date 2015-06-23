<?php $this->layout('admin/node/layout') ?>

<?php $this->section('admin-node-content') ?>

    <table>
        <tr>
            <th></th>
            <th>Administrador</th>
        </tr>
        <?php foreach ($this->node->admins as $userId => $userName) : ?>
        <tr>
            <td><a href="/admin/users/manage/<?php echo $userId; ?>">[Gestionar]</a></td>
            <td><?php echo $userName; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

<?php $this->replace() ?>
