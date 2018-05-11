<!-- Bootstrap core JavaScript -->
<?php if($this->debug()): ?>
<script src="<?= SRC_URL ?>/assets/vendor/jquery-1.12.4.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/jquery.mobile.custom.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/pronto/jquery.fs.pronto-3.2.1.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/hammerjs/hammer.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/jquery-hammerjs/jquery.hammer.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/clipboard/dist/clipboard.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/moment/min/moment-with-locales.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/d3/d3.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/footable/compiled/footable.min.js"></script>
<?php else: ?>
<script src="<?= SRC_URL ?>/assets/vendor/jquery-1.12.4.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/jquery.mobile.custom.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/pronto/jquery.fs.pronto.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/hammerjs/hammer.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/jquery-hammerjs/jquery.hammer.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/clipboard/dist/clipboard.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/moment/min/moment-with-locales.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/d3/d3.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/footable/compiled/footable.min.js"></script>
<?php endif ?>

<!-- Goteo utils: Debug functions, some defaults -->
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
var goteo = goteo || {};
goteo.debug = <?= GOTEO_ENV !== 'real' ? 'true' : 'false' ?> ;
SRC_URL = '<?= $this->ee(SRC_URL, 'js') ?>';
IMG_URL = '<?= $this->ee(defined('GOTEO_DATA_URL') ? GOTEO_DATA_URL : SITE_URL . '/img', 'js') ?>';
goteo.locale = '<?= $this->lang_current() ?>';
MAX_FILE_SIZE = <?= \Goteo\Model\Image::getSystemMaxFileSize('mb') ?>;
goteo.user_location = <?= json_encode($this->get_user_location()) ?>;
goteo.decimal = '<?= $this->get_currency('dec') ?>';
goteo.thousands = '<?= $this->get_currency('thou') ?>';
goteo.texts = goteo.texts || {};
goteo.texts['ajax-load-error'] = '<?= $this->ee($this->text('ajax-load-error'), 'js') ?>';
goteo.texts['regular-loading'] = '<?= $this->ee($this->text('regular-loading'), 'js') ?>';
goteo.urlParams;
(window.onpopstate = function () {
    var match,
        pl     = /\+/g,  // Regex for replacing addition symbol with a space
        search = /([^&=]+)=?([^&]*)/g,
        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
        query  = window.location.search.substring(1);

    goteo.urlParams = {};
    while (match = search.exec(query))
       goteo.urlParams[decode(match[1])] = decode(match[2]);
})();
// @license-end
</script>

<!-- POST PROCESSING THIS JAVASCRIPT BY GRUNT -->
<!-- build:js assets/js/all.js -->
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/goteo.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/jquery.animate-css.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/jquery.animate-number.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/menu.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/sidebar.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/widgets.js"></script>
<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/geolocation.js"></script>
<!-- endbuild -->

<!-- geolocation -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= $this->ee($this->get_config('geolocation.google_maps_key')) ?>&amp;v=3.exp&amp;libraries=places"></script>
