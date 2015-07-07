<?php

use Goteo\Library\Text;

?>

<div id="footer">
	<div id="super-footer">

        <ul>
            <li><a href="/about"><?php echo Text::get('regular-header-about'); ?></a></li>
            <li><a href="/user/login"><?php echo Text::get('regular-login'); ?></a></li>
            <li><a href="/contact"><?php echo Text::get('regular-footer-contact'); ?></a></li>
            <li><a href="/legal/terms"><?php echo Text::get('regular-footer-terms'); ?></a></li>
            <li><a href="/legal/privacy"><?php echo Text::get('regular-footer-privacy'); ?></a></li>
        </ul>

        <div class="platoniq">
           <span class="text"><a href="#" class="poweredby"><?php echo Text::get('footer-platoniq-iniciative') ?></a></span>
           <span class="logo"><a href="http://fundacion.goteo.org/" target="_blank" class="foundation">FFA</a></span>
           <span class="logo"><a href="http://www.youcoop.org" target="_blank" class="growby">Platoniq</a></span>
        </div>

	</div>
</div>
