<?php

use Goteo\Library\Text,
    Goteo\Model\Image,
    Goteo\Core\View;

$call = $this['call'];

//$cuantos = $call->getSupporters(true); // esto contaria independientemente del filtro y no seria igual que las caritas
$supporters = $call->getSupporters();

// para quitar los que no tengan imagen, los que tengan gotas o al convocador
foreach ($supporters as $kay=>$item) {
    if (!$item->avatar instanceof Image || $item->avatar->id == 1 || $item->id == $call->owner) unset($supporters[$kay]);
}

// así contamos tal cual las caritas que se van a pintar
$cuantos = count($supporters);

?>
<div id="supporters">
	<h8 class="title"><?php echo Text::get('call-header-supporters', $cuantos) ?></h8>
<ul>
<?php foreach ($supporters as $item) : ?>
    <li><a href="<?php echo '/user/profile/'.$item->id ?>" target="_blank"><img src="<?php echo $item->avatar->getLink(32, 32, true); ?>" alt="[G]" title="<?php echo $item->name; ?>" /></a></li>
<?php endforeach; ?>
</ul>
</div>
