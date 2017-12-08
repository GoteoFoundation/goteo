        <script type="text/javascript">
        // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
        if(navigator.userAgent.indexOf('Mac') != -1)
        {
            document.write ('<link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/mac.css" />');
        }
        // @license-end
        </script>

    <?php if ($this->jquery == 'latest') : ?>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/assets/vendor/jquery-1.12.4.min.js"></script>
    <?php else: ?>
        <!-- <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery-1.6.4.min.js"></script> -->
        <script type="text/javascript" src="<?php echo SRC_URL ?>/assets/vendor/jquery-1.12.4.min.js"></script>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery-migrate-1.4.1.js"></script>
        <!-- fancybox-->
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.fancybox.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo SRC_URL ?>/view/css/fancybox/jquery.fancybox.min.css" media="screen" />
        <!-- end custom fancybox-->
        <!-- sliders -->
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.slides.min.js"></script>
        <!-- end sliders -->
        <?php if ($this->superform) : ?>
            <script src="<?php echo SRC_URL ?>/view/js/datepicker.min.js"></script>
            <script src="<?php echo SRC_URL ?>/view/js/datepicker/datepicker.<?= $this->lang_current(true) ?>.js"></script>
        <?php endif ?>

    <?php endif ?>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.tipsy.min.js"></script>
        <!-- custom scrollbars -->
        <link type="text/css" href="<?php echo SRC_URL ?>/view/css/jquery.jscrollpane.min.css" rel="stylesheet" media="all" />
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.mousewheel.min.js"></script>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/jquery.jscrollpane.min.js"></script>
        <!-- end custom scrollbars -->



    <!-- TODO: this should go into the footer -->
    <?php if ($this->jscrypt) : ?>
        <script src="<?php echo SRC_URL ?>/view/js/sha1.min.js"></script>
    <?php endif; ?>

    <?php if ($this->superform) : ?>
        <script src="<?php echo SRC_URL ?>/view/js/superform.js"></script>
    <?php endif; ?>

    <?php if ($this->jsreq_autocomplete) : ?>
        <link href="<?php echo SRC_URL ?>/view/css/jquery-ui-1.10.3.autocomplete.min.css" rel="stylesheet" />
        <script src="<?php echo SRC_URL ?>/view/js/jquery-ui-1.10.3.autocomplete.min.js"></script>
    <?php endif; ?>

    <?php if ($this->jsreq_ckeditor) : ?>

       <script type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/ckeditor/ckeditor.js"></script>
    <?php endif; ?>
