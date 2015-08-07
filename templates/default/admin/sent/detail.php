<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<div class="widget board">
<a href="/mail/<?= $this->mail->getToken(true, true) ?>" target="_blank">[Visualizar]</a>
<p><b>Subject:</b> <?= $this->mail->getSubject() ?> %</p>
<p><b>Alcance:</b> <?= number_format(sprintf('%02f', $this->readed), 2, ',', '') ?> %</p>
</div>

<?php if ($this->metric_list) : ?>
    <div class="widget board">
    <table>
        <tr>
            <th>Metric</th>
            <th>Percent</th>
            <th>Non zero</th>
            <th>Total</th>
        </tr>
        <?php foreach ($this->metric_list as $collection) : ?>
        <tr>
            <td><?= $collection->metric->metric ?></td>
            <td><?= number_format(sprintf('%02f', $collection->getPercent()), 2, ',', '') ?> %</td>
            <td><?= $collection->non_zero ?></td>
            <td><?= $collection->total ?></td>
        </tr>
        <?php endforeach ?>
    </table>
    </div>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

<?php else : ?>
    <p>No se han encontrado registros</p>
<?php endif ?>


<?php $this->replace() ?>
