<?php

use Goteo\Library\Text,
    Goteo\Model\Image,
    Goteo\Core\View;

$call = $this['call'];

$cuantos = $call->getSupporters(true);
$supporters = $call->getSupporters();
?>
<div id="supporters">
	<h8 class="title"><?php echo Text::get('call-header-supporters', $cuantos) ?></h8>
<ul>
<?php foreach ($supporters as $item) :
    if (!$item->avatar instanceof Image) continue;  // quitamos los que tengan la imagen rota
    if ($item->avatar->id == 1) continue;  // por ahora no quitamos las gotas
    if ($item->id == $call->owner) continue;  // quitamos también al convocador
    ?>
    <li><a href="<?php echo '/user/profile/'.$item->id ?>" target="_blank"><img src="<?php echo $item->avatar->getLink(32, 32, true); ?>" alt="[G]" title="<?php echo $item->name; ?>" /></a></li>
<?php endforeach; ?>
</ul>
</div>
