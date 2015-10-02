<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$key = $this['apikey']['key'];
$user= $this['apikey']['user'];

?>
<div class="widget apikey">
        <h3 class="beak"><?= Text::get('api-key-tool-tip') ?></h3>
        <div class="user-key">
        	<div title="User" class="element-box">
            	<img style="vertical-align:middle;" src="/view/css/dashboard/user.png" width="20" \>
            	<span class="content user"><?= $user; ?></span>
          	</div>
          	<div title="Key" class="element-box">
            	<img style="vertical-align:middle;" src="/view/css/dashboard/key.png" width="40" \>
            	<span class="content">
            	<?= !empty($key) ? $key : Text::get('api-key-no-generated') ?>            		    	   
            	</span>
          	</div>
          	<div class="button-key">
          		<a class="button green" href="/dashboard/activity/apikey/generate">Generar nueva Key</a>
        	</div>
        	<div class="docu-link">
        	<a href="http://developers.goteo.org" target="_blank">Documentación API</a>
        </div>
</div>