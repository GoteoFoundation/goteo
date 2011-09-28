<?php
use Goteo\Library\Text;

$items = $this['items'];

?>
<div class="scroll-pane">
    <?php foreach ($items as $item) :
        $odd = !$odd ? true : false;
        ?>
    <div class="subitem<?php if ($odd) echo ' odd';?>">
       <span class="datepub"><?php echo Text::get('feed-timeago', $item->timeago); ?></span>
       <div class="content-pub"><?php echo $item->html; ?></div>
    </div>
    <?php endforeach; ?>
</div>
