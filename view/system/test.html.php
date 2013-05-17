<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\Model;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

<script type="text/javascript">
    var plat = navigator.platform;
    var disp = plat.toLowerCase().substring(0, 3)
    alert('Using: '+plat);
    if (disp == 'win' || disp == 'mac' || disp == 'lin') {
        document.write('<div>Using: '+plat+' is '+disp+', computer device</div>');
    } else {
        document.write('<div>Using: '+plat+' is '+disp+', movile device</div>');
    }
</script>

<?php
/*
 * Nos da, using: Win32, Win64, iPad, iPhone, iPod, MacIntel, MacPPC, Linux, Linux i686, Linux x86_64
 * 
 * Problema: para samsung, tanto smartphone como tablet da: linuxarmv7|
 * 
 * 
 * 
 */






    include 'view/footer.html.php';
include 'view/epilogue.html.php';
