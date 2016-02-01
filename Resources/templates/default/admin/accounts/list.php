<?php

use Goteo\Core\View;

$filters = $this->filters;

$emails = $this->emails;

$the_filters = array(
    'projects' => array (
        'label' => 'Proyecto',
        'first' => 'Todos los proyectos'),
    'methods' => array (
        'label' => 'Método de pago',
        'first' => 'Todos los métodos'),
    'status' => array (
        'label' => 'Estado del aporte',
        'first' => 'Todos los estados'),
    'procStatus' => array (
        'label' => 'Estado de financiacion',
        'first' => 'Todos los estados'),
    'calls' => array (
        'label' => 'De la convocatoria',
        'first' => 'Ninguna'),
    'review' => array (
        'label' => 'Para revisión',
        'first' => 'Todos'),
    'types' => array (
        'label' => 'Extra',
        'first' => 'Todos'),
    'issue' => array (
        'label' => 'Mostrar',
        'first' => 'Todos los aportes')
);

$coord = [];

?>
<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<a href="/admin/accounts/add" class="button">Generar aporte manual</a>
<a href="/admin/accounts/viewer" class="button">Visor de logs</a>&nbsp;&nbsp;&nbsp;
<?php if (!empty($filters['projects'])) : ?>
    <a href="/admin/accounts/report/<?= $filters['projects'] ?>#detail" class="button" target="_blank">Ver informe financiero completo del proyecto <strong><?= $this->projects[$filters['projects']] ?></strong></a>
<?php endif ?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/accounts" method="get">
        <?php foreach ($the_filters as $filter=>$data) : ?>
        <div style="float:left;margin:5px;">
            <label for="<?= $filter ?>-filter"><?= $data['label'] ?></label><br />
            <select id="<?= $filter ?>-filter" name="<?= $filter ?>">
                <option value="<?php if ($filter == 'procStatus' || $filter == 'projectStatus' || $filter == 'status' || $filter == 'issue') echo 'all' ?>"<?php if (($filter == 'procStatus' || $filter == 'status') && $filters[$filter] == 'all') echo ' selected="selected"'?>><?= $data['first'] ?></option>
            <?php foreach ($this->raw($filter) as $itemId => $itemName) : ?>
                <option value="<?= $itemId ?>"<?php if ($filters[$filter] === (string) $itemId) echo ' selected="selected"';?>><?= $itemName ?></option>
            <?php endforeach ?>
            </select>
        </div>
        <?php endforeach ?>

        <div style="float:left;margin:5px;">
            <label for="amount-filter">Importe desde:</label><br />
            <input type="text" id ="amount-filter" name="amount" value ="<?= $filters['amount']?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="amount-filter">Importe hasta:</label><br />
            <input type="text" id ="maxamount-filter" name="maxamount" value ="<?= $filters['maxamount']?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del usuario:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?= $filters['name']?>" />
        </div>

        <div style="float:left;margin:5px;" id="date-filter-from">
            <label for="date-filter-from">Fecha desde</label><br />
            <?= $this->html('input', ['value' => $filters['date_from'], 'name' => 'date_from', 'attribs' => ['id'=>'date-filter-from', 'class' => 'datepicker']]) ?>
        </div>
        <div style="float:left;margin:5px;" id="date-filter-until">
            <label for="date-filter-until">Fecha hasta</label><br />
            <?= $this->html('input', ['value' => $filters['date_until'], 'name' => 'date_until', 'attribs' => ['id'=>'date-filter-until', 'class' => 'datepicker']]) ?>
        </div>

        <div style="float:left;margin:5px;">
            <label for="id-filter">Id:</label><br />
            <input type="text" id ="id-filter" name="id" value ="<?= $filters['id']?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/accounts?reset=filters">[<?= $this->text('admin-remove-filters') ?>]</a>
</div>

<div class="widget board">
<?php if ($this->list) : ?>
    <p><strong><?= $this->text('regular-total') ?>:</strong>  <?= \amount_format($this->total_money) ?> (<em><?= number_format($this->total, 0, '', '.') ?> aportes</em>)</p>

    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Cofinanciador</th>
                <th>Proyecto</th>
                <th>Metodo</th>
                <th>Estado</th>
                <th>Pool</th>
            </tr>
        </thead>

        <tbody>
            <?php
            foreach ($this->list as $invest) :
                $title = $invest->id . ' - ' . $invest->amount . '€ - ' . $invest->full_name;
                if($loc = $invest->getLocation()) {
                    $coords[] = ['title' => $title,
                                'lat' => $loc->latitude,
                                'lng' => $loc->longitude
                                ];
                } elseif($add = $invest->getAddress()) {
                    $coords[] = ['title' => $title,
                                 'address' => $add->address . ', ' . $add->location. ', ' . $add->zipcode. ', ' . $add->country
                                ];
                }

            ?>
            <tr>
                <td><a href="/admin/accounts/details/<?= $invest->id ?>" title="<?php
                    if ($invest->issue)  echo 'Incidencia! ';
                    if ($invest->anonymous == 1)  echo 'Anónimo ';
                    if ($invest->resign == 1)  echo 'Donativo ';
                    if (!empty($invest->admin)) echo 'Manual';
                    if (!empty($invest->campaign)) echo 'Riego ';
                    if (!empty($invest->droped)) echo 'Regado ('.$invest->droped.')';
                    ?>" <?php if ($invest->issue) echo ' style="color:red !important;"' ?>>[Detalles]</a></td>
                <td><?= $invest->amount ?></td>
                <td><?= $invest->invested ?></td>
                <td><a href="mailto:<?= $invest->getUser()->email ?>" title="<?= $invest->getUser()->id .' / ' . $invest->getUser()->email .' / ' . $invest->getUser()->node ?>"><?= $invest->getUser()->name ?></a><a href="/admin/users/manage/<?= $invest->user ?>" target="_blank" title="<?= $invest->getUser()->name ?>">[<?= $invest->user ?>]</a></td>
                <td><?php if($invest->project): ?>
                    <a href="/admin/projects?proj_id=<?= $invest->project ?>" target="_blank"><?= $this->text_truncate($this->projects[$invest->project], 20); if (!empty($invest->campaign)) echo '<br />('.$invest->campaign.')' ?></a>
                    <?php else: ?>
                        <span class="label label-info"><?= $this->text('invest-pool-method') ?></span>
                    <?php endif ?>
                </td>
                <td><?= $this->methods[$invest->method] ?></td>
                <td><?= $this->percent_span(100 * ($invest->status + 1)/2, 0, $this->status[$invest->status]) ?></td>
                <td><?= $invest->pool ? 'Yes' : 'No' ?></td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>

<?php
    echo $this->insert('partials/utils/map_canvas', ['coords' => $coords]);
?>

<?php else : ?>
    <p>No hay transacciones que cumplan con los filtros.</p>
<?php endif;?>
</div>

<?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

<?php $this->replace() ?>
