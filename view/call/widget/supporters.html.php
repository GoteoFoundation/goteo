<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$call = $this['call'];

$cuantos = $call->getSupporters(true);
$supporters = $call->getSupporters();
?>
<span><?php echo $cuantos ?> usuarios participan blablalba</span>
<?php echo \trace($supporters); ?>