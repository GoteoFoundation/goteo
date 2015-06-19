<?php

use Goteo\Library\Text,
    Goteo\Model;

$promo = $vars['promo'];
$available = $vars['available'];

$users = Model\user::getVips();
$status = Model\Project::status();

?>
<form method="post" action="/admin/patron">
    <input type="hidden" name="action" value="<?php echo $vars['action'] ?>" />
    <input type="hidden" name="id" value="<?php echo $promo->id; ?>" />

<p>
    <label for="promo-project">Proyecto:</label><br />
    <select id="promo-project" name="project">
        <option value="" >Seleccionar el proyecto apadrinado</option>
    <?php foreach ($available as $project) : ?>
        <option value="<?php echo $project->id; ?>"<?php if ($promo->project == $project->id) echo' selected="selected"';?>><?php echo $project->name . ' ('. $status[$project->status] . ')'; ?></option>
    <?php endforeach; ?>
    </select>
</p>

<p>
    <label for="promo-user">Padrino:</label><br />
    <select id="promo-user" name="user">
        <option value="" >Seleccionar el usuario que lo apadrina</option>
    <?php foreach ($users as $user=>$userName) : ?>
        <option value="<?php echo $user; ?>"<?php if ($promo->user->id == $user) echo' selected="selected"';?>><?php echo $userName; ?></option>
    <?php endforeach; ?>
    </select>
</p>

<p>
    <label for="promo-name">Título:</label><span style="font-style:italic;">Máximo 20 caracteres</span><br />
    <input type="text" name="title" id="promo-title" value="<?php echo $promo->title; ?>" maxlength="20" style="width:500px;" />
</p>

<p>
    <label for="promo-description">Descripción:</label><span style="font-style:italic;">Máximo 100 caracteres</span><br />
    <input type="text" name="description" id="promo-description" maxlength="100" value="<?php echo $promo->description; ?>" style="width:750px;" />
</p>

<p>
    <label for="promo-link">Enlace:</label><br />
    <input type="text" name="link" id="promo-link" value="<?php echo $promo->link; ?>" maxlength="100" style="width:750px;" />
</p>

<p>
    <label>Publicado:</label><br />
    <label><input type="radio" name="active" id="promo-active" value="1"<?php if ($promo->active) echo ' checked="checked"'; ?>/> SÍ</label>
    &nbsp;&nbsp;&nbsp;
    <label><input type="radio" name="active" id="promo-inactive" value="0"<?php if (!$promo->active) echo ' checked="checked"'; ?>/> NO</label>
</p>

    <input type="submit" name="save" value="Guardar" />

    <p>
        <label for="mark-pending">Marcar como pendiente de traducir</label>
        <input id="mark-pending" type="checkbox" name="pending" value="1" />
    </p>

</form>
