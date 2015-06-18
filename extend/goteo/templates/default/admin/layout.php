<?php
/*
    Extended base layout for admin
 */
$this->layout('default::admin/layout');

$addons = array('calls', 'campaigns', 'transcalls', 'bazar', 'info', 'invests', 'opentags', 'patron', 'stories', 'tasks', 'reports');

$extra = array_intersect($addons, array_keys($this->admin_menu));
?>

<?php $this->section('admin-menu-left') ?>
<?php if ($extra): ?>
    <fieldset id="menu-goteo-addons">
        <legend>Extra</legend>
        <ul class="ul-admin">

    <?php foreach ($extra as $action) : ?>
        <li<?= ($action === $this->option ? ' class="selected"' : '') ?>><a href="/admin/<?= $action ?>"><?= $this->admin_menu[$action] ?></a></li>
    <?php endforeach ?>

        </ul>
    </fieldset>
<?php endif ?>

<?php $this->append() ?>


<?php $this->section('admin-menu-top') ?>

    <?php foreach (array('calls', 'reports') as $id) : ?>

            <?php if(array_key_exists($id, $this->admin_menu)) : ?>
                <li<?= ($id === $this->option ? ' class="selected"' : '') ?>><a href="/admin/<?= $id ?>"><?=  $this->admin_menu[$id] ?></a></li>
            <?php endif ?>

    <?php endforeach ?>

<?php $this->append() ?>
