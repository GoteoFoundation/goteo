<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$call = $this['call'];

?>
<h2 class="title"><?php echo Text::get('call-splash-campaign_title') ?><br /><?php echo $call->name ?></h2>

<?php if ($call->status == 3) : //inscripcion ?>
<p class="subtitle red"><?php echo Text::get('call-splash-searching_projects') ?></p>
<?php elseif (!empty($call->amount)) : //en campaña con dinero ?>
<p class="subtitle"><?php echo Text::html('call-splash-invest_explain', $call->user->name) ?></p>
<?php else : //en campaña sin dinero, con recursos ?>
<p class="subtitle"><?php echo Text::recorta($call->resources, 200) ?></p>
<?php endif; ?>
