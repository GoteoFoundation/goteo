<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<div class="widget board">
<a href="/mail/<?= $this->mail->getToken(true, true) ?>" target="_blank">[Visualizar]</a>
<hr>stats:<hr>
<?php print_r($this->stats) ?>
</div>

<?php if ($this->stats_list) : ?>
    <div class="widget board">
    <table>
        <tr>
            <th>Email</th>
            <th>Metric</th>
            <th>Counter</th>
            <th>Created</th>
        </tr>
        <?php foreach ($this->stats_list as $stat) : ?>
        <tr>
            <td><?= $stat->email ?></td>
            <td><?= $stat->metric ?></td>
            <td><?= $stat->counter ?></td>
            <td><?= $stat->created_at ?></td>
        </tr>
        <?php endforeach ?>
    </table>
    </div>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

<?php else : ?>
    <p>No se han encontrado registros</p>
<?php endif ?>


<?php $this->replace() ?>
