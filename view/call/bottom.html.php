<?php
use Goteo\Core\View;

$call = $this['call'];
?>
<script type="text/javascript">
    $(function(){
        $('#call-supporters').slides({
            container: 'call-supporters-container',
            generatePagination: false,
            effect: 'slide',
            play: 7000
        });
    });
</script>
<div id="supporters-sponsors">
    <div id="call-supporters">
        <div class="call-supporters-container">
        <?php if ($call->status > 3 && $call->getSupporters(true) > 0) echo new View('view/call/widget/supporters.html.php', $this); ?>
        <?php if ($call->post instanceof Goteo\Model\Blog\Post) echo new View('view/call/widget/post.html.php', $this); ?>
        </div>
    </div>
    
    <?php echo new View('view/call/widget/sponsors.html.php', $this); ?>
        
</div>
