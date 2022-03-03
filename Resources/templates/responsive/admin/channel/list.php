<?php
    $this->layout('admin/channel/layout');

    $this->section('admin-container-body');
?>

<h3><?= $this->text('admin-list-total', $this->total) ?></h3>

<?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'image', 'name', 'actions'])]) ?>


<?php
    $this->append()
?>
