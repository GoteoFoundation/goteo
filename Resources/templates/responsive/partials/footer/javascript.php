<!-- Bootstrap core JavaScript -->

<script src="<?php echo SRC_URL ?>/assets/vendor/jquery.min.js"></script>
<script src="<?php echo SRC_URL ?>/assets/vendor/jquery.mobile.custom.min.js"></script>
<script src="<?php echo SRC_URL ?>/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo SRC_URL ?>/assets/vendor/footable/footable.min.js"></script>
<script src="<?php echo SRC_URL ?>/assets/vendor/pronto/jquery.fs.pronto.min.js"></script>

<script src="<?php echo SRC_URL ?>/assets/vendor/d3/d3.v3.min.js"></script>

<!--script src="<?php echo SRC_URL ?>/assets/js/docs.min.js"></script-->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--script src="<?php echo SRC_URL ?>/assets/js/ie10-viewport-bug-workaround.js"></script-->

<!-- Goteo utils: Debug functions, Session keeper -->
<script type="text/javascript" src="<?php echo SRC_URL ?>/assets/js/goteo.js"></script>
<script type="text/javascript"><?php
    echo 'goteo.debug = ' . (GOTEO_ENV !== 'real' ? 'true' : 'false') . ';';
    echo 'SRC_URL = "' . SRC_URL . '";';
?></script>

 <script>

  $(document).ready(function() {

   var docHeight = $(window).height();
   var footerHeight = $('#footer').height();
   var footerTop = $('#footer').position().top + footerHeight;

   if (footerTop < docHeight) {
    $('#footer').css('margin-top', 10+ (docHeight - footerTop) + 'px');
   }
  });

  $('table.footable').footable();

 </script>

<!-- geolocation -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;libraries=places"></script>
<script type="text/javascript" src="<?php echo SRC_URL ?>/assets/js/geolocation.js"></script>

<?= $this->insert('partials/footer/analytics') ?>

