<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\Message;

$project = $vars['project'];

if (!$project instanceof Model\Project) {
    Message::Error('Instancia de proyecto corrupta');
    throw new Redirection('/admin/projects');
}

?>
<script type="text/javascript">
function assign() {
    if (document.getElementById('assign-user').value != '') {
        document.getElementById('form-assign').submit();
        return true;
    } else {
        alert('No has seleccionado ningún asesor');
        return false;
    }
}
</script>
<div class="widget">
    <!-- asignar -->
    <table>
        <tr>
            <th>Asesor</th>
            <th></th>
        </tr>
        <?php foreach ($project->consultants as $userId=>$userName) : ?>
        <tr>
            <td><?php echo $userName; ?></td>
            <td><a href="/admin/projects/consultants/<?php echo $project->id; ?>/?op=unassignConsultant&user=<?php echo $userId; ?>">[Desasignar]</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <form id="form-assign" action="/admin/projects/consultants/<?php echo $project->id; ?>" method="get">
                <input type="hidden" name="op" value="assignConsultant" />
            <td colspan="2">
                <select id="assign-user" name="user">
                    <option value="">Asigna otro asesor</option>
                    <?php foreach ($vars['admins'] as $userId=>$userName) :
                        if (isset($project->consultants[$userId])) continue;
                        ?>
                    <option value="<?php echo $userId; ?>"><?php echo $userName; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
            </form>
        </tr>
    </table>
</div>
