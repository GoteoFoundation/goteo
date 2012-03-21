<?php

use Goteo\Library\Text,
    Goteo\Model;

$promo = $this['promo'];

// proyectos disponibles
// si tenemos ya proyecto seleccionado lo incluimos
$projects = Model\Patron::available($promo->project, $_SESSION['admin_node']);
$users = Model\user::getVips();
$status = Model\Project::status();

?>
<form method="post" action="/admin/patron">
    <input type="hidden" name="action" value="<?php echo $this['action'] ?>" />
    <input type="hidden" name="order" value="<?php echo $promo->order ?>" />
    <input type="hidden" name="id" value="<?php echo $promo->id; ?>" />

<p>
    <label for="promo-project">Proyecto:</label><br />
    <select id="promo-project" name="project">
        <option value="" >Seleccionar el proyecto apadrinado</option>
    <?php foreach ($projects as $project) : ?>
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
    <label for="promo-link">Enlace:</label><br />
    <input type="text" name="title" id="promo-link" value="<?php echo $promo->link; ?>" size="250" />
</p>

    <input type="submit" name="save" value="Guardar" />
</form>
