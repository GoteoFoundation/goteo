<?php

use Goteo\Library\Text,
    Goteo\Library\Lang;

$node = $this['node'];

$filters = $this['filters'];

?>
<script type="text/javascript">
function assign() {
    if (document.getElementById('assign-user').value != '') {
        document.getElementById('form-assign').submit();
        return true;
    } else {
        alert('No has seleccionado ningun traductor');
        return false;
    }
}
</script>
<div class="widget">
<?php if ($this['action'] == 'edit') : ?>
    <h3 class="title">Traductores para el nodo <?php echo $node->name ?></h3>
        <!-- asignar -->
        <table>
            <tr>
                <th>Traductor</th>
                <th></th>
            </tr>
            <?php foreach ($node->translators as $userId=>$userName) : ?>
            <tr>
                <td><?php if ($userId == $node->owner) echo '(AUTOR) '; ?><?php echo $userName; ?></td>
                <td><a href="/admin/transnodes/unassign/<?php echo $node->id; ?>/?user=<?php echo $userId; ?>">[Desasignar]</a></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <form id="form-assign" action="/admin/transnodes/assign/<?php echo $node->id; ?>" method="get">
                <td colspan="2">
                    <select id="assign-user" name="user">
                        <option value="">Selecciona otro traductor</option>
                        <?php foreach ($this['translators'] as $user) :
                            if (in_array($user->id, array_keys($node->translators))) continue;
                            ?>
                        <option value="<?php echo $user->id; ?>"><?php if ($user->id == $node->owner) echo '(AUTOR) '; ?><?php echo $user->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
                </form>
            </tr>
        </table>
        <hr />
        <a href="/admin/transnodes/close/<?php echo $node->id; ?>" class="button" onclick="return confirm('Seguro que deseas dar por finalizada esta traducción?')">Cerrar la traducción</a>&nbsp;&nbsp;&nbsp;
        <a href="/admin/transnodes/send/<?php echo $node->id; ?>" class="button green" onclick="return confirm('Se va a enviar un email automaticamente, ok?')">Avisar al administrador</a>
        <hr />
<?php elseif ($this['action'] == 'add') : ?>
    <form method="post" action="/admin/transnodes/<?php echo $this['action']; ?>/<?php echo $node->id; ?>">

        <table>
            <tr>
                <td>
                    <label for="add-proj">Nodo que habilitamos</label><br />
                    <select id="add-proj" name="node">
                        <option value="">Selecciona el nodo</option>
                        <?php foreach ($this['availables'] as $node) : ?>
                            <option value="<?php echo $node->id; ?>"<?php if ($_GET['node'] == $node->id) echo ' selected="selected"';?>><?php echo $node->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>


       <input type="submit" name="save" value="Guardar" />
    </form>
<?php endif; ?>
</div>