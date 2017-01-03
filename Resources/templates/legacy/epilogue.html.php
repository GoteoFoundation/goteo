        </div>


        <!-- Goteo utils: Debug functions, Session keeper -->
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/goteo.js"></script>
        <script type="text/javascript">
        // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
        <?php
            echo 'goteo.debug = ' . (GOTEO_ENV !== 'real' ? 'true' : 'false') . ';';
        ?>
        // @license-end
        </script>

        <!-- geolocation -->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;libraries=places"></script>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/assets/js/geolocation.js"></script>

    </body>
</html>
