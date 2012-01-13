<?php

use Goteo\Library\Text,
    Goteo\Library\Lang;

$call = $this['call'];

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?owner={$filters['owner']}&translator={$filters['translator']}";

?>
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
                <td><a href="/admin/transcalls/unassign/<?php echo $call->id; ?>/<?php echo $filter; ?>&user=<?php echo $userId; ?>">[Desasignar]</a></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <form id="form-assign" action="/admin/transcalls/assign/<?php echo $call->id; ?>/<?php echo $filter; ?>" method="get">
                <td colspan="2">
                    <select name="user">
                        <option value="">Selecciona otro traductor</option>
                        <?php foreach ($this['translators'] as $user) :
                            if (in_array($user->id, array_keys($call->translators))) continue;
                            ?>
                        <option value="<?php echo $user->id; ?>"><?php if ($user->id == $call->owner) echo '(AUTOR) '; ?><?php echo $user->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><a href="#" onclick="document.getElementById('form-assign').submit(); return false;">[Asignar]</a></td>
                </form>
            </tr>
        </table>
        <hr />
        <a href="/admin/transcalls/close/<?php echo $call->id; ?>" class="button red" onclick="return confirm('Seguro que deseas dar por finalizada esta traducción?')">Cerrar la traducción</a>&nbsp;&nbsp;&nbsp;
        <a href="/admin/transcalls/send/<?php echo $call->id; ?>" class="button green" onclick="return confirm('Se va a enviar un email?')">Avisar al autor</a>
        <hr />
<?php endif; ?>

    <form method="post" action="/admin/transcalls/<?php echo $this['action']; ?>/<?php echo $call->id; ?>/?filter=<?php echo $this['filter']; ?>">

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
            </tr>
        </table>


       <input type="submit" name="save" value="Guardar" />
    </form>
</div>