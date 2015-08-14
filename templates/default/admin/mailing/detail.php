<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<a href="/admin/mailing" class="button">Volver a comuncaciones</a>

<a href="<?= $this->link ?>" class="button" target="_blank">Ver el mensaje</a>

<div class="widget">
    <p>'Buscábamos comunicarnos con <?= $this->raw('filters_txt') ?> </p>
    <p>Se ha iniciado un nuevo mailing masivo a <?= $this->total ?> usuarios con el asunto "<strong><?=$this->raw('subject') ?></strong>".</p>

    <?php if($mailing->mail && $this->is_module_admin('Sent')): ?>
        <a href="/admin/sent/detail/<?= $this->mail ?>" class="button">Ver en el historial de envíos (estadísticas)</a>
        <br>
    <?php endif ?>

    <?php if($this->is_module_admin('Newsletter')): ?>
        <a href="/admin/newsletter/detail/<?= $this->sender ?>" class="button">Ver en el admin del boletín</a>
    <?php endif ?>

</div>

<?php $this->replace() ?>
