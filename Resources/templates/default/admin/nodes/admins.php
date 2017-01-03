<?php

$node = $this->node;

?>
<?php $this->layout('admin/nodes/layout') ?>

<?php $this->section('admin-node-content') ?>
    <table>
        <tr>
            <th>Administrador</th>
            <th></th>
        </tr>
        <?php foreach ($node->admins as $userId => $userName) : ?>
        <tr>
            <td><?= $userName ?></td>
            <td><a href="/admin/nodes/admins/<?= $node->id ?>?op=unassign&amp;user=<?= $userId ?>">[Desasignar]</a></td>
        </tr>
        <?php endforeach ?>
        <tr>
            <form id="form-assign" action="/admin/nodes/admins/<?= $node->id ?>" method="get">
                <input type="hidden" name="op" value="assign" />
            <td colspan="2">
                <select id="assign-user" name="user">
                    <option value="">Asigna otro administrador</option>
                    <?php foreach ($this->admins as $userId => $userName) : ?>
                    <option value="<?= $userId ?>"><?= $userName ?></option>
                    <?php endforeach ?>
                </select>
            </td>
            <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
            </form>
        </tr>
    </table>
</div>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
function assign() {
    if (document.getElementById('assign-user').value != '') {
        document.getElementById('form-assign').submit();
        return true;
    } else {
        alert('No has seleccionado ningun administrador');
        return false;
    }
}
// @license-end
</script>

<?php $this->append() ?>
