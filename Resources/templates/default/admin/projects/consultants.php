<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>

    <table>
        <tr>
            <th>Asesor</th>
            <th></th>
        </tr>
        <?php foreach ($this->project->getConsultants() as $userId => $userName) : ?>
        <tr>
            <td><?php echo $userName; ?></td>
            <td><a href="/admin/projects/consultants/<?php echo $this->project->id; ?>?op=unassignConsultant&user=<?php echo $userId; ?>">[Desasignar]</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <form id="form-assign" action="/admin/projects/consultants/<?php echo $this->project->id; ?>" method="get">
                <input type="hidden" name="op" value="assignConsultant" />
            <td colspan="2">
                <select id="assign-user" name="user">
                    <option value="">Asigna otro asesor</option>
                    <?php foreach ($this->admins as $userId => $userName) :
                        if (array_key_exists($userId, $this->project->getConsultants())) continue;
                        ?>
                    <option value="<?php echo $userId; ?>"><?php echo $userName; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
            </form>
        </tr>
    </table>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
function assign() {
    if (document.getElementById('assign-user').value != '') {
        document.getElementById('form-assign').submit();
        return true;
    } else {
        alert('No has seleccionado ningún asesor');
        return false;
    }
}
// @license-end
</script>
<?php $this->append() ?>
