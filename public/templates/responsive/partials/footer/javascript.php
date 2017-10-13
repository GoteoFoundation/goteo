<!-- Bootstrap core JavaScript -->

<script src="<?= SRC_URL ?>/assets/vendor/jquery-1.12.4.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/jquery.mobile.custom.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/footable/compiled/footable.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/pronto/jquery.fs.pronto.min.js"></script>

<script src="<?= SRC_URL ?>/assets/vendor/d3/d3.v3.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/hammerjs/hammer.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/jquery-hammerjs/jquery.hammer.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/clipboard/clipboard.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/moment/min/moment-with-locales.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick.min.js"></script>


<!-- Goteo utils: Debug functions, Session keeper -->
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
var goteo = {};
<?php
    echo 'goteo.debug = ' . (GOTEO_ENV !== 'real' ? 'true' : 'false') . ';';
    echo 'SRC_URL = "' . SRC_URL . '";';
    echo "goteo.locale = '" . $this->lang_current() . "';";
    echo 'MAX_FILE_SIZE = ' . \Goteo\Model\Image::getSystemMaxFileSize('mb') . ';';
?>

// @license-end
</script>

<!-- POST PROCESSING THIS JAVASCRIPT BY GRUNT -->
<!-- build:js assets/js/all.js -->
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/goteo.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/jquery.animate-css.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/menu.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/sidebar.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/widgets.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/geolocation.js"></script>
<!-- endbuild -->

<!-- geolocation -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;libraries=places"></script>
