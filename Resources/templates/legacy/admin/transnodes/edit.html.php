<?php

use Goteo\Library\Text;

$node = $vars['node'];

$filters = $vars['filters'];

?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
function assign() {
    if (document.getElementById('assign-user').value != '') {
        document.getElementById('form-assign').submit();
        return true;
    } else {
        alert('No has seleccionado ningun traductor');
        return false;
    }
}
// @license-end
</script>
<div class="widget">
    <h3 class="title">Traductores para el nodo <?php echo $node->name ?></h3>
        <!-- asignar -->
        <table>
            <tr>
                <th>Traductor</th>
                <th></th>
            </tr>
            <?php foreach ($node->translators as $userId=>$userName) : ?>
            <tr>
                <td><?php echo $userName; ?></td>
                <td><a href="/admin/transnodes/unassign/<?php echo $node->id; ?>?user=<?php echo $userId; ?>">[Desasignar]</a></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <form id="form-assign" action="/admin/transnodes/assign/<?php echo $node->id; ?>" method="get">
                <td colspan="2">
                    <select id="assign-user" name="user">
                        <option value="">Selecciona otro traductor</option>
                        <?php foreach ($vars['translators'] as $user) :
                            if (in_array($user->id, array_keys($node->translators))) continue;
                            ?>
                        <option value="<?php echo $user->id; ?>"><?php echo $user->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
                </form>
            </tr>
        </table>
        <hr />
</div>
