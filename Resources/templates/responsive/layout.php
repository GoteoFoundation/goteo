<?php

// Views called by AJAX methods will return Bootstrap modal windows
if($this->is_pronto()):
    echo json_encode([
        'title' => $this->title,
        'content' => $this->supply('content')
        ]);
    return;
endif;
if($this->is_ajax()):
    $this->section('content');
    $this->stop();
    return;
endif;

// Normal operation, show the full page
?><!DOCTYPE html>
<html lang="<?= $this->lang_current() ?>">

    <head>
    <?php $this->section('head') ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?= $this->insert('partials/header/metas') ?>

        <?php $this->section('lang-metas') ?>
            <?= $this->insert('partials/header/lang_metas') ?>
        <?php $this->stop() ?>

        <title><?= $this->title ?></title>

        <link rel="icon" type="image/png" href="/favicon.ico" >

        <?= $this->insert('partials/header/styles') ?>

        <?= $this->insert('partials/header/javascript') ?>


    <?php $this->stop() ?>

    </head>

    <body role="document" <?php if ($this->bodyClass) echo ' class="' . $this->bodyClass . '"' ?>>

    <noscript><div id="noscript">Please enable JavaScript</div></noscript>

    <?php $this->section('header') ?>
        <?= $this->insert("partials/header") ?>
    <?php $this->stop() ?>

    <?= $this->supply('messages', $this->insert("partials/header/messages")) ?>

    <div id="main-content">
    <?php $this->section('content') ?>
    <?php $this->stop() ?>
    </div>

    <?php $this->section('footer') ?>

        <?= $this->insert('partials/footer') ?>

        <?= $this->insert('partials/footer/analytics') ?>

        <?= $this->insert('partials/footer/javascript') ?>

    <?php $this->stop() ?>

    </body>
</html>
