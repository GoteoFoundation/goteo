<?php

use Goteo\Library\Text,
    Goteo\Model\Image,
    Goteo\Core\View;

$call = $vars['call'];

$cuantos = $call->getSupporters(true);
$supporters = $call->getFaces();

?>
<div id="supporters">
	<h8 class="title"><?php echo Text::get('call-header-supporters', $cuantos) ?></h8>
<ul>
<?php foreach ($supporters as $item) : ?>
    <li><a href="<?php echo '/user/profile/'.$item->id ?>" target="_blank"><img src="<?php echo $item->avatar->getLink(32, 32, true); ?>" alt="[G]" title="<?php echo htmlspecialchars($item->name) ?>" /></a></li>
<?php endforeach; ?>
</ul>
</div>
