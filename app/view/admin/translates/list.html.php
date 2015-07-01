<?php

use Goteo\Library\Text;

$filters = $vars['filters'];
?>
<a href="/admin/translates/add" class="button">Nuevo proyecto para traducir</a>

<div class="widget board">
<form id="filter-form" action="/admin/translates" method="get">
    <label for="owner-filter">Proyectos del usuario:</label>
    <select id="owner-filter" name="owner" onchange="document.getElementById('filter-form').submit();">
        <option value="">Todos los productores</option>
    <?php foreach ($vars['owners'] as $ownerId=>$ownerName) : ?>
        <option value="<?php echo $ownerId; ?>"<?php if ($filters['owner'] == $ownerId) echo ' selected="selected"';?>><?php echo $ownerName ? $ownerName : $ownerId; ?></option>
    <?php endforeach; ?>
    </select>

    <label for="translator-filter">Asignados a traductor:</label>
    <select id="translator-filter" name="translator" onchange="document.getElementById('filter-form').submit();">
        <option value="">Todos los traductores</option>
    <?php foreach ($vars['translators'] as $translator) : ?>
        <option value="<?php echo $translator->id; ?>"<?php if ($filters['translator'] == $translator->id) echo ' selected="selected"';?>><?php echo $translator->name; ?></option>
    <?php endforeach; ?>
    </select>
</form>
</div>

<!-- proyectos con la traducción activa -->
<?php if (!empty($vars['projects'])) : ?>
        <div class="widget board">
            <table>
                <thead>
                    <tr>
                        <th width="5%"><!-- Editar y asignar --></th>
                        <th width="55%">Proyecto</th> <!-- edit -->
                        <th width="30%">Creador</th>
                        <th width="10%">Idioma</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($vars['projects'] as $project) : ?>
                    <tr>
                        <td><a href="/admin/translates/edit/<?php echo $project->id; ?>">[Editar]</a></td>
                        <td><a href="/project/<?php echo $project->id; ?>" target="_blank" title="Preview"><?php echo $project->name; ?></a></td>
                        <td><?php echo $project->user->name; ?></td>
                        <td><?php echo $project->lang; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        </div>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>
