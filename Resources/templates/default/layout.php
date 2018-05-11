<?php

$sidebar = $this->get_sidebar_menu();
$bodyClass = $this->bodyClass;
if($sidebar) {
    $bodyClass = $bodyClass ? "$bodyClass has-sidebar" : 'has-sidebar';
}

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?= $this->lang_current() ?>">

    <head>
    <?php $this->section('head') ?>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= $this->title ?></title>
        <link rel="icon" type="image/png" href="/myicon.png" />

        <?= $this->insert('partials/header/metas') ?>

        <?php $this->section('lang-metas') ?>
            <?= $this->insert('partials/header/lang_metas') ?>
        <?php $this->stop() ?>

        <?= $this->insert('partials/header/styles') ?>

        <?= $this->insert('partials/header/javascript') ?>

    <?php $this->stop() ?>

    </head>

    <body<?php if ($bodyClass) echo ' class="' . $bodyClass . '"' ?>>

    <?= $this->supply('sidebar', $this->insert("partials/sidebar", ['sidebarMenu' => $sidebar])) ?>

    <div id="wrapper">

    <?= $this->insert('wrapper') ?>

    </div>
    <?php $this->section('footer') ?>
    
    <?= $this->insert('partials/footer') ?>
    
    <?= $this->insert('partials/footer/analytics') ?>
    
    <?= $this->insert('partials/footer/javascript') ?>
    
    <?php $this->stop() ?>
    
    </body>
</html>
