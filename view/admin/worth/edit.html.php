<?php

use Goteo\Library\Text,
    Goteo\Model;

$worth = $this['worth'];

?>
<form method="post" action="/admin/worth/edit" >
    <input type="hidden" name="id" value="<?php echo $worth->id; ?>" />

<p>
    <label for="worth-name">Nombre del nivel:</label><br />
    <input id="worth-name" name="name" value="<?php echo $worth->name ?>" />
</p>

<p>
    <label for="worth-amount">Caudal:</label><br />
    <input id="worth-amount" name="amount" value="<?php echo $worth->amount ?>" />
</p>

    <input type="submit" name="save" value="Guardar" />
</form>
