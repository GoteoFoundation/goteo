<?php

use Goteo\Library\Text,
    Goteo\Library\Content;

$bodyClass = 'admin';

$filter = $this['filter'];

$data = Content::getAll($this['filters'], $_SESSION['translator_lang']);

// valores de filtro
$types     = Content::$types; // por tipo de campo
$tables    = Content::$tables; // por tabla

//auxiliar
$fields    = Content::$fields; // los campos

// metemos el todos
\array_unshift($types, 'Todos los tipos');
\array_unshift($tables, 'Todas las tablas');


$filters = array(
            'type' => array(
                    'label'   => 'Filtrar por tipo de contenido:',
                    'type'    => 'select',
                    'options' => $types,
                    'value'   => $this['filters']['type']
                ),
            'table' => array(
                    'label'   => 'Filtrar por tabla:',
                    'type'    => 'select',
                    'options' => $tables,
                    'value'   => $this['filters']['table']
                ),
            'text' => array(
                    'label'   => 'Buscar texto:',
                    'type'    => 'input',
                    'options' => null,
                    'value'   => $this['filters']['text']
                )
        );

?>
<h3 class="title">Traducción de contenidos</h3>
<!-- Filtro -->
<?php if (!empty($filters)) : ?>
<div class="widget board">
    <form id="filter-form" action="/translate/contents/list/<?php echo $filter ?>" method="get">
        <?php foreach ($filters as $id=>$fil) : ?>
        <?php if ($fil['type'] == 'select') : ?>
            <label for="filter-<?php echo $id; ?>"><?php echo $fil['label']; ?></label>
            <select id="filter-<?php echo $id; ?>" name="<?php echo $id; ?>" onchange="document.getElementById('filter-form').submit();">
            <?php foreach ($fil['options'] as $val=>$opt) : ?>
                <option value="<?php echo $val; ?>"<?php if ($fil['value'] == $val) echo ' selected="selected"';?>><?php echo $opt; ?></option>
            <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <?php if ($fil['type'] == 'input') : ?>
            <br />
            <label for="filter-<?php echo $id; ?>"><?php echo $fil['label']; ?></label>
            <input name="<?php echo $id; ?>" value="<?php echo (string) $fil['value']; ?>" />
            <input type="submit" name="filter" value="Buscar">
        <?php endif; ?>
        <?php endforeach; ?>
    </form>
</div>
<?php endif; ?>

<!-- lista -->
    <?php foreach ($tables as $table=>$tableName) :
        if (!empty($data[$table])) : ?>
<div class="widget board">
    <h3 class="title">Contenidos de <?php echo $tableName ?></h3>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Registro</th>
                <th>Campo</th>
                <th>Texto</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($data[$table] as $item) : ?>
            <tr>
                <td width="5%"><a title="Registro <?php echo $item->table.'-'.$item->id ?>" href='/translate/contents/edit/<?php echo $item->table.'-'.$item->id . $filter ?>'>[Edit]</a></td>
                <td width="25%"><?php echo $item->id ?></td>
                <td width="25%"><?php echo $fields[$item->table][$item->field] ?></td>
                <td width="70%"><?php echo Text::recorta($item->value, 250) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <?php endif; 
    endforeach; ?>
