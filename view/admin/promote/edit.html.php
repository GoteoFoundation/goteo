<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

define('ADMIN_NOAUTOSAVE', true);

$promo = $this['promo'];

?>
<form method="post" action="/admin/promote">
    <input type="hidden" name="order" value="<?php echo $promo->order ?>" />
    <input type="hidden" name="id" value="<?php echo $promo->id; ?>" />

<p>
    <label for="promo-project">Proyecto:</label><br />
    <select id="promo-project" name="project">
        <option value="" >Seleccionar el proyecto a destacar</option>
    <?php foreach ($this['projects'] as $project) : ?>
        <option value="<?php echo $project->id; ?>"<?php if ($promo->project == $project->id) echo' selected="selected"';?>><?php echo $project->name . ' ('. $this['status'][$project->status] . ')'; ?></option>
    <?php endforeach; ?>
    </select>
</p>

<p>
    <label for="promo-name">Título:</label><span style="font-style:italic;">Máximo 20 caracteres</span><br />
    <input type="text" name="title" id="promo-title" value="<?php echo $promo->title; ?>" size="50" maxlength="20" />
</p>

<p>
    <label for="promo-description">Descripción:</label><span style="font-style:italic;">Máximo 100 caracteres</span><br />
    <input type="text" name="description" id="promo-description" maxlength="100" value="<?php echo $promo->description; ?>" size="120" />
</p>

    <input type="submit" name="save" value="Guardar" />
</form>
