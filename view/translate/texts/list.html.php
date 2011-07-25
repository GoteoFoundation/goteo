<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

// no cache para textos
define('GOTEO_ADMIN_NOCACHE', true);

$filter = $this['filter'];

$data = Text::getAll($this['filters'], $_SESSION['translator_lang']);

// valores de filtro
$idfilters = Text::filters();
$groups    = Text::groups();

// metemos el todos
\array_unshift($idfilters, 'Todos los textos');
\array_unshift($groups, 'Todas las agrupaciones');


$filters = array(
            'idfilter' => array(
                    'label'   => 'Filtrar por tipo:',
                    'type'    => 'select',
                    'options' => $idfilters,
                    'value'   => $this['filters']['idfilter']
                ),
            'group' => array(
                    'label'   => 'Filtrar por agrupación:',
                    'type'    => 'select',
                    'options' => $groups,
                    'value'   => $this['filters']['group']
                ),
            'text' => array(
                    'label'   => 'Buscar texto:',
                    'type'    => 'input',
                    'options' => null,
                    'value'   => $this['filters']['text']
                )
        );

?>
<h3 class="title">Traducción de textos dinámicos</h3>
<!-- Filtro -->
<?php if (!empty($filters)) : ?>
<div class="widget board">
    <form id="filter-form" action="/translate/texts/list/<?php echo $filter ?>" method="get">
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
<div class="widget board">
    <?php if (!empty($data)) : ?>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Texto</th>
                <th>Agrupación</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($data as $item) : ?>
            <tr>
                <td width="5%"><a title="Registro <?php echo $item->id ?>" href='/translate/texts/edit/<?php echo $item->id . $filter ?>'>[Edit]</a></td>
                <td width="70%"><?php echo $item->text ?></td>
                <td width="25%"><?php echo $groups[$item->group] ?></td>
                <td></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
