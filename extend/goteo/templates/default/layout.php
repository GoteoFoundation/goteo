<?php

$node = $this->get_config('current_node');

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
    <?php $this->section('head') ?>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?=$this->title?></title>
        <link rel="icon" type="image/png" href="/nodesys/<?=$node?>/myicon.png" />

        <?=$this->insert("partials/header/metas")?>

        <?=$this->insert("$node::partials/header/styles")?>

        <?=$this->insert("partials/header/javascript")?>

    <?php $this->stop() ?>

    </head>

    <body<?php if ($this->bodyClass) echo ' class="' . $this->bodyClass . '"' ?>>


    <noscript><div id="noscript">Please enable JavaScript</div></noscript>

    <div id="wrapper">


    <?php $this->section('header') ?>
        <?=$this->insert('partials/header')?>
    <?php $this->stop() ?>

    <?php $this->section('sub-header') ?>
    <?php $this->stop() ?>

    <?php echo $this->supply('messages', $this->insert("partials/header/messages")) ?>

    <?php $this->section('content') ?>
    <?php $this->stop() ?>


    </div>

    <?php $this->section('footer') ?>

        <?=$this->insert("partials/footer")?>

        <?=$this->insert("$node::partials/footer/analytics")?>

        <?=$this->insert("partials/footer/javascript")?>

    <?php $this->stop() ?>

    </body>
</html>
