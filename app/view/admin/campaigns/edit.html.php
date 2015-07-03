<?php

use Goteo\Library\Text,
    Goteo\Core\Redirection,
    Goteo\Model;

$campaign = $vars['campaign'];
$calls = $vars['calls'];
$status = $vars['status'];

// solo para nodos
// TODO: esto aqui no!
if (!isset($_SESSION['admin_node'])) {
    throw new Redirection('/admin');
}

$node = $_SESSION['admin_node'];
?>
<form method="post" action="/admin/campaigns">
    <input type="hidden" name="action" value="<?php echo $vars['action'] ?>" />
    <input type="hidden" name="order" value="<?php echo $campaign->order ?>" />
    <input type="hidden" name="id" value="<?php echo $campaign->id; ?>" />

<p>
    <label for="campaign-call">Convocatoria:</label><br />
    <select id="campaign-call" name="call">
        <option value="" >Seleccionar la convocatoria a destacar</option>
    <?php foreach ($calls as $call) : ?>
        <option value="<?php echo $call->id; ?>"<?php if ($campaign->call == $call->id) echo' selected="selected"';?>><?php echo substr($call->name, 0, 100) . ' ('. $status[$call->status] . ')'; ?></option>
    <?php endforeach; ?>
    </select>
</p>

<p>
    <label>Publicar:</label><br />
    <label><input type="radio" name="active" id="campaign-active" value="1"<?php if ($campaign->active) echo ' checked="checked"'; ?>/> SÍ</label>
    &nbsp;&nbsp;&nbsp;
    <label><input type="radio" name="active" id="campaign-inactive" value="0"<?php if (!$campaign->active) echo ' checked="checked"'; ?>/> NO</label>
</p>

    <input type="submit" name="save" value="Guardar" />
</form>
