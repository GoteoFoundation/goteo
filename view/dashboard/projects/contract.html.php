<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$contract = $this['contract'];
$show = $this['show']; // estado del proceso (ver conroller/dashboard/projects::prepare_contract ) 

// explodear el contenido y mostrar segun el show
$content = explode('<hr />', $this['page']->content);
// esta es la correspondencia:
$shwCnt = array(
    'off' => 0,
    'campaign' => 1,
    'edit' => 2,
    'closed' => 3,
    'review' => 4,
    'ready' => 5,
    'recieved' => 6,
    'payed' => 7,
    'fulfilled' => 8,
    'separador' => 9,
    'siempre' => 10
);
?>
<div class="widget">
    <?php echo $content[$shwCnt[$show]]; ?>
</div>
<div class="widget">
    <?php echo $content[10]; ?>
</div>
