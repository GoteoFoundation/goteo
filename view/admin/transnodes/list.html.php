<?php

use Goteo\Library\Text;

$filters = $this['filters'];
?>
<a href="/admin/transnodes/add" class="button">Nueva nodo para traducir</a>

<div class="widget board">
<form id="filter-form" action="/admin/transnodes" method="get">
    <label for="admin-filter">Nodo gestionado por:</label>
    <select id="admin-filter" name="admin" onchange="document.getElementById('filter-form').submit();">
        <option value="">Todos los admins</option>
    <?php foreach ($this['admins'] as $adminId=>$adminName) : ?>
        <option value="<?php echo $adminId; ?>"<?php if ($filters['admin'] == $adminId) echo ' selected="selected"';?>><?php echo $adminName; ?></option>
    <?php endforeach; ?>
    </select>

    <label for="translator-filter">Asignados al traductor:</label>
    <select id="translator-filter" name="translator" onchange="document.getElementById('filter-form').submit();">
        <option value="">Todos los traductores</option>
    <?php foreach ($this['translators'] as $translator) : ?>
        <option value="<?php echo $translator->id; ?>"<?php if ($filters['translator'] == $translator->id) echo ' selected="selected"';?>><?php echo $translator->name; ?></option>
    <?php endforeach; ?>
    </select>
</form>
</div>

<!-- nodos con la traducción activa -->
<?php if (!empty($this['nodes'])) : ?>
        <div class="widget board">
            <table>
                <thead>
                    <tr>
                        <th width="5%"><!-- Editar y asignar --></th>
                        <th width="55%">nodo</th> <!-- edit -->
                        <th width="30%">Creador</th>
<!--                        <th width="10%">Idioma</th> -->
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($this['nodes'] as $node) : ?>
                    <tr>
                        <td><a href="/admin/transnodes/edit/<?php echo $node->id; ?>">[Editar]</a></td>
                        <td><a href="/node/<?php echo $node->id; ?>" target="_blank" title="Preview"><?php echo $node->name; ?></a></td>
                        <td><?php echo $node->user->name; ?></td>
<!--                        <td><?php echo $node->lang; ?></td> -->
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
            
        </div>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>