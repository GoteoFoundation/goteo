        </div>


        <!-- Goteo utils: Debug functions, Session keeper -->
        <script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/goteo.js"></script>
        <script type="text/javascript"><?php
            echo 'goteo.debug = ' . (GOTEO_ENV !== 'real' ? 'true' : 'false') . ';';
        ?></script>

        <!-- geolocation -->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;libraries=places"></script>
        <script type="text/javascript" src="<?php echo SRC_URL ?>/assets/js/geolocation.js"></script>

    </body>
</html>
