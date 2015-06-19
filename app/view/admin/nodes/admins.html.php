<?php

use Goteo\Library\Text,
    Goteo\Model\User;

$node = $vars['node'];

?>
<script type="text/javascript">
function assign() {
    if (document.getElementById('assign-user').value != '') {
        document.getElementById('form-assign').submit();
        return true;
    } else {
        alert('No has seleccionado ningun administrador');
        return false;
    }
}
</script>
<div class="widget">
    <!-- asignar -->
    <table>
        <tr>
            <th>Administrador</th>
            <th></th>
        </tr>
        <?php foreach ($node->admins as $userId=>$userName) : ?>
        <tr>
            <td><?php echo $userName; ?></td>
            <td><a href="/admin/nodes/admins/<?php echo $node->id; ?>/?op=unassign&user=<?php echo $userId; ?>">[Desasignar]</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <form id="form-assign" action="/admin/nodes/admins/<?php echo $node->id; ?>" method="get">
                <input type="hidden" name="op" value="assign" />
            <td colspan="2">
                <select id="assign-user" name="user">
                    <option value="">Asigna otro administrador</option>
                    <?php foreach ($vars['admins'] as $userId=>$userName) : ?>
                    <option value="<?php echo $userId; ?>"><?php echo $userName; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
            </form>
        </tr>
    </table>
</div>
