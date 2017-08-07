<?php $this->layout('home/layout') ?>

<?php $this->section('content') ?>

<!-- Banner section -->

<?= $this->insert('home/partials/main_slider') ?>

<?= $this->insert('home/partials/search') ?>

<?= $this->insert('home/partials/call_to_action') ?>

<?= $this->insert('home/partials/adventages') ?>

<?= $this->insert('home/partials/foundation') ?>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script>

$(document).ready(function(){

  $('.fade').slick({
  dots: true,
  infinite: true,
  speed: 1500,
  fade: true,
  arrows: true,
  cssEase: 'linear',
	});

});

</script>

<?php $this->append() ?>