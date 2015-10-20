<?php

// Views called by AJAX methods will return Bootstrap modal windows
if($this->is_ajax()):
    $this->section('content');
    $this->stop();
    return;
endif;

// Normal operation, show the full page
?><!DOCTYPE html>
<html lang="es">

    <head>
    <?php $this->section('head') ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?= $this->insert('partials/header/metas') ?>

        <title><?= $this->title ?></title>

        <link rel="icon" type="image/png" href="/favicon.ico" >

        <?= $this->insert('partials/header/styles') ?>

        <?= $this->insert('partials/header/javascript') ?>


    <?php $this->stop() ?>

    </head>

    <body role="document">

    <noscript><div id="noscript">Please enable JavaScript</div></noscript>

    <?php $this->section('header') ?>
        <?= $this->insert("partials/header") ?>
    <?php $this->stop() ?>

    <?= $this->supply('messages', $this->insert("partials/header/messages")) ?>

    <?php $this->section('content') ?>
    <?php $this->stop() ?>

    <?php $this->section('footer') ?>

        <?= $this->insert('partials/footer') ?>

        <?= $this->insert('partials/footer/analytics') ?>

        <?= $this->insert('partials/footer/javascript') ?>

    <?php $this->stop() ?>

    </body>
</html>
