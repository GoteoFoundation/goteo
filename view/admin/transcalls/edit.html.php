<?php

use Goteo\Library\Text,
    Goteo\Library\Lang;

$call = $this['call'];
$langs = Lang::getAll();

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
    <h3 class="title">Traductores para la convocatoria <?php echo $call->name ?></h3>
        <!-- asignar -->
        <table>
            <tr>
                <th>Traductor</th>
                <th></th>
            </tr>
            <?php foreach ($call->translators as $userId=>$userName) : ?>
            <tr>
                <td><?php if ($userId == $call->owner) echo '(AUTOR) '; ?><?php echo $userName; ?></td>
                <td><a href="/admin/transcalls/unassign/<?php echo $call->id; ?>/?user=<?php echo $userId; ?>">[Desasignar]</a></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <form id="form-assign" action="/admin/transcalls/assign/<?php echo $call->id; ?>" method="get">
                <td colspan="2">
                    <select id="assign-user" name="user">
                        <option value="">Selecciona otro traductor</option>
                        <?php foreach ($this['translators'] as $user) :
                            if (in_array($user->id, array_keys($call->translators))) continue;
                            ?>
                        <option value="<?php echo $user->id; ?>"><?php if ($user->id == $call->owner) echo '(AUTOR) '; ?><?php echo $user->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
                </form>
            </tr>
        </table>
        <hr />
        <a href="/admin/transcalls/close/<?php echo $call->id; ?>" class="button" onclick="return confirm('Seguro que deseas dar por finalizada esta traducción?')">Cerrar la traducción</a>&nbsp;&nbsp;&nbsp;
        <a href="/admin/transcalls/send/<?php echo $call->id; ?>" class="button green" onclick="return confirm('Se va a enviar un email automaticamente, ok?')">Avisar al convocador</a>
        <hr />
<?php endif; ?>
        
    <form method="post" action="/admin/transcalls/<?php echo $this['action']; ?>/<?php echo $call->id; ?>">

        <table>
            <tr>
                <td><?php if ($this['action'] == 'add') : ?>
                    <label for="add-proj">Convocatoria que habilitamos</label><br />
                    <select id="add-proj" name="call">
                        <option value="">Selecciona la convocatoria</option>
                        <?php foreach ($this['availables'] as $call) : ?>
                            <option value="<?php echo $call->id; ?>"<?php if ($_GET['call'] == $call->id) echo ' selected="selected"';?>><?php echo $call->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else : ?>
                    <input type="hidden" name="call" value="<?php echo $call->id; ?>" />
                <?php endif; ?></td>
                <td><!-- Idioma original -->
                    <label for="orig-lang">Idioma original de la convocatoria</label><br />
                    <select id="orig-lang" name="lang">
                        <?php foreach ($langs as $item) : ?>
                            <option value="<?php echo $item->id; ?>"<?php if ($call->lang == $item->id || (empty($call->lang) && $item->id == 'es' )) echo ' selected="selected"';?>><?php echo $item->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>


       <input type="submit" name="save" value="Guardar" />
    </form>
</div>