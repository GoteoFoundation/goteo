<?php

$this->layout('layout');

$this->section('head');

?>


<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick-theme.css"/>

<?php

$this->append(); 

$this->section('header');

//Header empty

$this->replace();

$this->section('content');
?>

<?= $this->supply('channel-content') ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>


<?= $this->insert('channel/call/partials/footer') ?>

<?= $this->insert('partials/footer/javascript') ?>

<?= $this->insert('partials/footer/analytics') ?>

<script src="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick.min.js"></script>
<script type="text/javascript" src="<?= $this->asset('js/channel/call.js') ?>"></script>
<?php $this->replace() ?>