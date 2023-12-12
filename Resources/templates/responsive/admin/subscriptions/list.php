<?php

$filters = $this->filters;
$the_filters = array(
    'projects' => array(
        'label' => 'Proyecto',
        'first' => 'Todos los proyectos'
    ),
    'statuses' => array(
        'label' => 'Estado de la subscripción',
        'first' => 'Todos los estados'
    )
);

?>
<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/subscriptions" method="get">
        <?php foreach ($the_filters as $filter => $data) : ?>
            <div style="float:left;margin:5px;">
                <label for="<?= $filter ?>-filter"><?= $data['label'] ?></label><br />
                <select id="<?= $filter ?>-filter" name="<?= $filter ?>">
                    <option>
                        <?= $data['first'] ?>
                    </option>
                    <?php foreach ($this->raw($filter) as $itemId => $itemName) : ?>
                        <option value="<?= $itemId ?>" <?php if ($filters[$filter] === (string) $itemId) echo ' selected="selected"'; ?>>
                            <?= $itemName ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        <?php endforeach ?>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <label for="name-filter">Usuario:</label><br />
            <input type="text" id="name-filter" name="name" value="<?= $filters['name'] ?>" />
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
        <table width="100%">
            <thead>
                <tr>
                    <th>Proyecto</th>
                    <th>Usuario</th>
                    <th>Recompensa</th>
                    <th>Importe</th>
                    <th>Estado</th>
                    <th>Stripe</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($this->list as $subscription) : ?>
                    <?php $plan = $subscription['plan'] ?>
                    <?php $price = $subscription['items']['data'][0]['price']; ?>
                    <tr>
                        <td><?= $subscription['metadata']['project'] ?></td>
                        <td><?= $subscription['metadata']['user'] ?></td>
                        <td><?= $subscription['metadata']['reward'] ?></td>
                        <td><?= \amount_format($price['unit_amount'] / 100) . '/' . $plan['interval'] ?></td>
                        <td><span class="label label-info"><?= $subscription['status'] ?></span></td>
                        <td>
                            <a class="btn btn-default" title="<?= $this->text('regular-view') ?>" href="https://dashboard.stripe.com/subscriptions/<?= $subscription['id'] ?>"><i class="fa fa-info-circle"></i></a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>

        </table>

    <?php else : ?>
        <p>No hay subscripciones que cumplan con los filtros.</p>
    <?php endif; ?>
</div>

<?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

<?php $this->replace() ?>