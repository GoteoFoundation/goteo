<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Application\Message;

$project = $vars['project'];

if (!$project instanceof Model\Project) {
    Message::Error('Instancia de proyecto corrupta');
    throw new Redirection('/admin/projects');
}

?>
<script type="text/javascript">
function assign() {
    if (document.getElementById('assign-open-tag').value != '') {
        document.getElementById('form-assign').submit();
        return true;
    } else {
        alert('No has seleccionado ninguna agrupación');
        return false;
    }
}
</script>
<div class="widget">
    <!-- asignar -->
    <table>
        <tr>
            <th>Agrupación</th>
            <th></th>
        </tr>
        <?php foreach ($project->open_tags as $open_tagId=>$open_tagName) : ?>
        <tr>
            <td><?php echo $open_tagName; ?></td>
            <td><a href="/admin/projects/open_tags/<?php echo $project->id; ?>/?op=unassignOpen_tag&open_tag=<?php echo $open_tagId; ?>">[Desasignar]</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <form id="form-assign" action="/admin/projects/open_tags/<?php echo $project->id; ?>" method="get">
                <input type="hidden" name="op" value="assignOpen_tag" />
            <td colspan="2">
                <select id="assign-open-tag" name="open_tag">
                    <option value="">Asigna otra agrupación</option>
                    <?php foreach ($vars['open_tags'] as $open_tagId=>$open_tagName) :
                        if (isset($project->open_tags[$open_tagId])) continue;
                        ?>
                    <option value="<?php echo $open_tagId; ?>"><?php echo $open_tagName; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
            </form>
        </tr>
    </table>
</div>


