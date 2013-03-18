<?php

use Goteo\Library\Text;

$filters = $this['filters'];
?>
<a href="/admin/locations/add" class="button">Nueva Localización</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/locations/search" class="button">Buscar por Localización</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/locations/check" class="button">Revisar Localizaciones</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/locations/autocheck" class="button">Revisión automática</a>

<div class="widget board">
    <form id="filter-form" action="/admin/locations" method="get">


        <div style="float:left;margin:5px;">
            <label for="name-filter">Nombre:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="valid-filter">Por validez:</label><br />
            <select id="valid-filter" name="valid">
            <?php foreach ($this['valid'] as $vId=>$vName) : ?>
                <option value="<?php echo $vId; ?>"<?php if ($filters['valid'] === $vId) echo ' selected="selected"';?>><?php echo $vName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="used-filter">Por uso:</label><br />
            <select id="used-filter" name="used">
            <?php foreach ($this['used'] as $uId=>$uName) : ?>
                <option value="<?php echo $uId; ?>"<?php if ($filters['used'] === $uId) echo ' selected="selected"';?>><?php echo $uName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>


        <br clear="both" />

        <div style="float:left;margin:5px;">
            <label for="country-filter">Por pais:</label><br />
            <select id="country-filter" name="country">
                <option value="">--</option>
            <?php foreach ($this['countries'] as $cId=>$cName) : ?>
                <option value="<?php echo $cId; ?>"<?php if ($filters['country'] == $cId) echo ' selected="selected"';?>><?php echo $cName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="region-filter">Por provincia:</label><br />
            <select id="region-filter" name="region">
                <option value="">--</option>
            <?php foreach ($this['regions'] as $rId=>$rName) : ?>
                <option value="<?php echo $rId; ?>"<?php if ($filters['region'] == $rId) echo ' selected="selected"';?>><?php echo $rName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="location-filter">Por poblacion:</label><br />
            <select id="location-filter" name="location">
                <option value="">--</option>
            <?php foreach ($this['locations'] as $lId=>$lName) : ?>
                <option value="<?php echo $lId; ?>"<?php if ($filters['location'] == $lId) echo ' selected="selected"';?>><?php echo $lName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        
        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (!empty($this['list'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th> <!-- edit -->
                <th>Localidad</th>
                <th>Provincia</th>
                <th>Pais</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $item) : ?>
            <tr>
                <td><a href="/admin/locations/edit/<?php echo $item->id; ?>" title="Editar">[Editar]</a></td>
                <td><?php echo $item->location; ?></td>
                <td><?php echo $item->region; ?></td>
                <td><?php echo $item->country; ?></td>
                <td><?php echo (empty($item->valid)) ? '<span style="color: red;">Revisar</span>' : '';?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>