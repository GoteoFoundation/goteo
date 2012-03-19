<?php

use Goteo\Library\Text,
    Goteo\Model;

$campaign = $this['campaign'];
$calls = $this['calls'];
$status = $this['status'];

// solo para nodos
if (!isset($_SESSION['admin_node'])) {
    throw new Redirection('/admin');
}

$node = $_SESSION['admin_node'];
?>
<form method="post" action="/admin/campaigns">
    <input type="hidden" name="action" value="<?php echo $this['action'] ?>" />
    <input type="hidden" name="order" value="<?php echo $campaign->order ?>" />
    <input type="hidden" name="id" value="<?php echo $campaign->id; ?>" />

<p>
    <label for="campaign-call">Campaña:</label><br />
    <select id="campaign-call" name="call">
        <option value="" >Seleccionar la campaña a destacar</option>
    <?php foreach ($calls as $call) : ?>
        <option value="<?php echo $call->id; ?>"<?php if ($campaign->call == $call->id) echo' selected="selected"';?>><?php echo $call->name . ' ('. $status[$call->status] . ')'; ?></option>
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
