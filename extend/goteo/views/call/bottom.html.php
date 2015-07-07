<?php
use Goteo\Core\View;

$call = $vars['call'];
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
        <?php if ($call->status > 3 && $call->getSupporters(true) > 0) echo View::get('call/widget/supporters.html.php', $vars); ?>
        <?php foreach ($call->posts as $post) {
            if ($post instanceof Goteo\Model\Blog\Post)
                echo View::get('call/widget/post.html.php', array('post' => $post));
        } ?>
        </div>
    </div>

    <?php echo View::get('call/widget/sponsors.html.php', $vars); ?>
</div>
<div id="supporters-sponsors">
    <div id="call-supporters">
        <?php echo View::get('call/widget/sponsors-responsive.html.php', $vars); ?>
    </div>
</div>
