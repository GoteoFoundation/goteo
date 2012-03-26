<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection;

$invest = $this['invest'];
$status = $this['status'];

?>
<a href="/admin/accounts/details/<?php echo $invest->id ?>" class="button">Volver al detalle</a>
<div class="widget" >
    <form method="post" action="/admin/accounts/update/<?php echo $invest->id ?>" >

    <p>
        <label for="status-filter">Pasarlo al estado:</label><br />
        <select id="status-filter" name="status" >
        <?php foreach ($this['statuss'] as $statusId=>$statusName) : ?>
            <option value="<?php echo $statusId; ?>"<?php if ($invest->status == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
        <?php endforeach; ?>
        </select>
    </p>

        <input type="submit" name="update" value="Aplicar" onclick="return confirm('Segurisimo que le campibamos el estado al aporte???')"/>
    </form>
</div>
