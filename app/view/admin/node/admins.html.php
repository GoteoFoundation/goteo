<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection;

$node = $vars['node'];

if (!$node instanceof Model\Node) {
    throw new Redirection('/admin');
}

?>
<a href="/admin/node" class="button">Volver</a>
<div class="widget">
    <!-- asignar -->
    <table>
        <tr>
            <th></th>
            <th>Administrador</th>
        </tr>
        <?php foreach ($node->admins as $userId=>$userName) : ?>
        <tr>
            <td><a href="/admin/users/manage/<?php echo $userId; ?>">[Gestionar]</a></td>
            <td><?php echo $userName; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
