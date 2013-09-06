<?php
use Goteo\Core\View;

$call = $this['call'];
?>
<div id="supporters-sponsors">
    <?php if ($call->status > 3 && $call->getSupporters(true) > 0) echo new View('view/call/widget/supporters.html.php', $this); 
    else  echo new View('view/call/widget/post.html.php', $this); ?>
    <?php echo new View('view/call/widget/sponsors.html.php', $this); ?>
</div>
