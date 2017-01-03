<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$translator = $this->get_user()->roles['translator'] ? true : false;

$filters = $this->filters;

$botones = array(
    'edit' => '[Editar]',
    'remove' => '[Quitar]',
    'translate' => '[Traducir]',
    'up' => '[&uarr;]',
    'down' => '[&darr;]'
);

// ancho de los tds depende del numero de columnas
$cols = count($this->columns);
$per = 100 / $cols;

?>
<?php if ($this->addbutton) : ?>
    <a href="<?= $this->url ?>/add" class="button"><?= $this->addbutton ?></a>
<?php endif ?>

<?php if ($this->otherbutton) : ?>
    <?= $this->otherbutton ?>
<?php endif ?>

<!-- Filtro -->
<?php if ($filters) : ?>
<div class="widget board">
    <form id="filter-form" action="<?= $this->url ?>" method="get">
        <?php foreach ($filters as $id=>$fil) : ?>
        <?php if ($fil['type'] == 'select') : ?>
            <label for="filter-<?= $id ?>"><?= $fil['label'] ?></label>
            <select id="filter-<?= $id ?>" name="<?= $id ?>" onchange="document.getElementById('filter-form').submit();">
            <?php foreach ($fil['options'] as $val=>$opt) : ?>
                <option value="<?= $val ?>"<?php if ($fil['value'] == $val) echo ' selected="selected"';?>><?= $opt ?></option>
            <?php endforeach ?>
            </select>
        <?php endif ?>
        <?php if ($fil['type'] == 'input') : ?>
            <br />
            <label for="filter-<?= $id ?>"><?= $fil['label'] ?></label>
            <input name="<?= $id ?>" value="<?= (string) $fil['value'] ?>" />
            <input type="submit" name="filter" value="Buscar">
        <?php endif ?>
        <?php endforeach ?>
    </form>
</div>
<?php endif ?>

<!-- lista -->
<div class="widget board">
    <?php if ($this->data) : ?>
    <table>
        <thead>
            <tr>
                <?php foreach ($this->columns as $key => $label) : ?>
                    <?php
                        if (in_array($key, ['translate','remove','edit','up','down']))  {
                            $addfinals = true;
                            continue;
                        }
                     ?>
                    <th><?= $label ?></th>
                <?php endforeach ?>
                <?php if($addfinals): ?>
                    <th>Ops.</th>
                <?php endif ?>
            </tr>
        </thead>

        <tbody>
        <?php
        foreach ($this->data as $item) : ?>
            <tr>
            <?php
             $finals = [];
             foreach ($this->columns as $key => $label) : ?>
                <?php
                 if (in_array($key, ['translate','remove','edit','up','down'])) : ?>
                    <?php
                    $id = (is_object($item)) ? $item->id : $item['id'];
                    if ($key == 'translate') {
                        if($translator) $finals[] = '<a href="/translate/' . $this->model . '/edit/'.$id . '" >[Traducir]</a>';
                    } elseif ($key == 'remove') {
                        $finals[] = '<a href="' . $this->url . '/remove/' . $id . '" onclick="return confirm(\'Seguro que deseas eliminar este registro?\');">' . $botones[$key] . '</a>';
                        // $finals[] = $botones[$key];
                    } else {
                        $finals[] = '<a title="Registro ' . $id . '" href="' . "{$this->url}/{$key}/{$id}/{$filter}" . '">' . $botones[$key] . '</a>';
                    }
                    ?>
                <?php elseif ($key == 'image') : ?>
                    <td width="<?= round($per)-5 ?>%"><?php if ($item->$key) : ?><img src="<?= SITE_URL ?>/image/<?= (is_object($item)) ? $item->$key : $item[$key] ?>/110/110" alt="image" /><?php endif ?></td>
                <?php else : ?>
                    <td width="<?= round($per)-5 ?>%"><?= (is_object($item)) ? $item->$key : $item[$key] ?></td>
                <?php endif ?>
            <?php endforeach ?>

            <?php if($finals) : ?>
                <td width="5%"><?= implode(' ', $finals) ?></td>
            <?php endif ?>

            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <?php else : ?>
    <p><?= $this->text('admin-empty-list') ?></p>
    <?php endif ?>
</div>

<?php $this->replace() ?>
