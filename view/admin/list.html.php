<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

$filters = $this['filters'];

// si hay filtro lo arrastramos
if (!empty($filters)) {
    $filter = "?";
    foreach ($filters as $key => $fil) {
        $filter .= "$key={$fil['value']}&";
    }
} else {
    $filter = '';
}

$botones = array(
    'edit' => '[Editar]',
    'remove' => '[Quitar]',
    'up' => '[&uarr;]',
    'down' => '[&darr;]'
);

// ancho de los tds depende del numero de columnas
$cols = count($this['columns']);
$per = 100 / $cols;

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2><?php echo $this['title']; ?></h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                    <?php foreach ($this['menu'] as $menu) : ?>
                        <li><a href="<?php echo $menu['url']; ?>"><?php echo $menu['label']; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['success'])) {
                echo '<pre>' . print_r($this['success'], 1) . '</pre>';
            } ?>

            <!-- Filtro -->
            <?php if (!empty($filters)) : ?>
            <div class="widget board">
                <form id="filter-form" action="<?php echo $this['url']; ?>" method="get">
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
                <?php if (!empty($this['data'])) : ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($this['columns'] as $key=>$label) : ?>
                                <th><?php echo $label; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($this['data'] as $item) : ?>
                        <tr>
                        <?php foreach ($this['columns'] as $key=>$label) : ?>
                            <?php if (in_array($key, array('edit', 'remove', 'up', 'down'))) : ?>
                                <td width="5%"><a title="Registro <?php echo (is_object($item)) ? $item->id : $item['id']; ?>" href='<?php $id = (is_object($item)) ? $item->id : $item['id']; echo "{$this['url']}/{$key}/{$id}/{$filter}"; ?>'><?php echo $botones[$key]; ?></a></td>
                            <?php else : ?>
                                <td width="<?php echo round($per)-5; ?>%"><?php echo (is_object($item)) ? $item->$key : $item[$key]; ?></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else : ?>
                <p>No se han encontrado registros</p>
                <?php endif; ?>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';