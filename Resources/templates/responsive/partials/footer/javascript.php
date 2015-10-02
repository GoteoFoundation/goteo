<!-- Bootstrap core JavaScript -->

<script src="<?php echo SRC_URL ?>/assets/js/jquery.min.js"></script>
<script src="<?php echo SRC_URL ?>/assets/js/bootstrap.min.js"></script>
<!--script src="<?php echo SRC_URL ?>/assets/js/docs.min.js"></script-->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--script src="<?php echo SRC_URL ?>/assets/js/ie10-viewport-bug-workaround.js"></script-->

<!-- Goteo utils: Debug functions, Session keeper -->
<script type="text/javascript" src="<?php echo SRC_URL ?>/assets/js/goteo.js"></script>
<script type="text/javascript"><?php
    echo 'goteo.debug = ' . (GOTEO_ENV !== 'real' ? 'true' : 'false') . ';';
    echo 'SRC_URL = "' . SRC_URL . '";';
?></script>

<!-- geolocation -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;libraries=places&amp;sensor=false"></script>
<script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/geolocation.js"></script>
