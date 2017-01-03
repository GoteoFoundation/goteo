<?php

use Goteo\Library\Text,
    Goteo\Application\Lang;

$project = $vars['project'];
$langs = Lang::listAll('object', false);

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
<?php if ($vars['action'] == 'edit') : ?>
    <h3 class="title">Traductores para el proyecto <?php echo $project->name ?></h3>
        <!-- asignar -->
        <table>
            <tr>
                <th>Traductor</th>
                <th></th>
            </tr>
            <?php foreach ($project->translators as $userId=>$userName) : ?>
            <tr>
                <td><?php if ($userId == $project->owner) echo '(AUTOR) '; ?><?php echo $userName; ?></td>
                <td><a href="/admin/translates/unassign/<?php echo $project->id; ?>?user=<?php echo $userId; ?>">[Desasignar]</a></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <form id="form-assign" action="/admin/translates/assign/<?php echo $project->id; ?>" method="get">
                <td colspan="2">
                    <select id="assign-user" name="user">
                        <option value="">Selecciona otro traductor</option>
                        <?php foreach ($vars['translators'] as $user) :
                            if (in_array($user->id, array_keys($project->translators))) continue;
                            ?>
                        <option value="<?php echo $user->id; ?>"><?php if ($user->id == $project->owner) echo '(AUTOR) '; ?><?php echo $user->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
                </form>
            </tr>
        </table>
        <hr />
        <a href="/admin/translates/close/<?php echo $project->id; ?>" class="button" onclick="return confirm('Seguro que deseas dar por finalizada esta traducción?')">Cerrar la traducción</a>&nbsp;&nbsp;&nbsp;
        <a href="/admin/translates/send/<?php echo $project->id; ?>" class="button green" onclick="return confirm('Se va a enviar un email automaticamente, ok?')">Avisar al autor</a>
        <hr />
<?php endif; ?>

    <form method="post" action="/admin/translates/<?= $post->id ? $vars['action'].'/'.$project->id : $vars['action'] ?>">

        <table>
            <tr>
                <td><?php if ($vars['action'] == 'add') : ?>
                    <label for="add-proj">Proyecto que habilitamos</label><br />
                    <select id="add-proj" name="project">
                        <option value="">Selecciona el proyecto</option>
                        <?php foreach ($vars['availables'] as $proj) : ?>
                            <option value="<?php echo $proj->id; ?>"<?php if ($_GET['project'] == $proj->id) echo ' selected="selected"';?>><?php echo $proj->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else : ?>
                    <input type="hidden" name="project" value="<?php echo $project->id; ?>" />
                <?php endif; ?></td>
                <td><!-- Idioma original -->
                    <label for="orig-lang">Idioma original del proyecto</label><br />
                    <select id="orig-lang" name="lang">
                        <?php foreach ($langs as $item) : ?>
                            <option value="<?php echo $item->id; ?>"<?php if ($project->lang == $item->id || (empty($project->lang) && $item->id == 'es' )) echo ' selected="selected"';?>><?php echo $item->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>


       <input type="submit" name="save" value="Guardar" />
    </form>
</div>
