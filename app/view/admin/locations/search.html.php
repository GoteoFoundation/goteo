<?php

use Goteo\Library\Text;

$filters = $this['filters'];
?>
<a href="/admin/locations/add" class="button">Nueva Localización</a>

<div class="widget board">
    <form id="filter-form" action="/admin/locations" method="get">


        <div style="float:left;margin:5px;">
            <label for="name-filter">Nombre:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="valid-filter">Por validez:</label><br />
            <select id="valid-filter" name="valid">
            <?php foreach ($this['valid'] as $Id=>$Name) : ?>
                <option value="<?php echo $Id; ?>"<?php if ($filters['valid'] == $Id) echo ' selected="selected"';?>><?php echo $Name; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="used-filter">Por uso:</label><br />
            <select id="used-filter" name="used">
            <?php foreach ($this['used'] as $Id=>$Name) : ?>
                <option value="<?php echo $Id; ?>"<?php if ($filters['used'] == $Id) echo ' selected="selected"';?>><?php echo $Name; ?></option>
            <?php endforeach; ?>
            </select>
        </div>


        <br clear="both" />

        <div style="float:left;margin:5px;">
            <label for="country-filter">Por pais:</label><br />
            <select id="country-filter" name="used">
            <?php foreach ($this['countries'] as $Id=>$Name) : ?>
                <option value="<?php echo $Id; ?>"<?php if ($filters['country'] == $Id) echo ' selected="selected"';?>><?php echo $Name; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="region-filter">Por provincia:</label><br />
            <select id="region-filter" name="used">
            <?php foreach ($this['regions'] as $Id=>$Name) : ?>
                <option value="<?php echo $Id; ?>"<?php if ($filters['region'] == $Id) echo ' selected="selected"';?>><?php echo $Name; ?></option>
            <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="location-filter">Por poblacion:</label><br />
            <select id="location-filter" name="used">
            <?php foreach ($this['locations'] as $Id=>$Name) : ?>
                <option value="<?php echo $Id; ?>"<?php if ($filters['location'] == $Id) echo ' selected="selected"';?>><?php echo $Name; ?></option>
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