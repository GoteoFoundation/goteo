<?php

$this->layout('layout');

$this->section('content');
?>

    <div class="container">
        <div class="row row-form">
            <div class="panel panel-default panel-form-wide">
                <div class="panel-body">
                    <?= $this->supply('dashboard-messages', $this->insert("partials/header/messages")) ?>

                    <?= $this->supply('inner-content') ?>
                </div>
            </div>
        </div>
    </div>


<?php $this->replace() ?>
