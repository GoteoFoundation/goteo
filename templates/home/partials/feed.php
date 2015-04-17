<?php
use Goteo\Library\Feed;

$feed = $vars['feed'];

?>
<div class="widget feed">
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.scroll-pane').jScrollPane({showArrows: true});
    });
    </script>
    <h3 class="title"><?php echo $this->text('feed-header'); ?></h3>

    <div style="height:auto;overflow:auto;margin-left:15px">

        <div class="block goteo">
           <h4><?php echo $this->text('feed-head-goteo'); ?></h4>
           <div class="item scroll-pane" style="height:350px;">
               <?php foreach ($feed['goteo'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>

        <div class="block projects">
            <h4><?php echo $this->text('feed-head-projects'); ?></h4>
            <div class="item scroll-pane" style="height:350px;">
               <?php foreach ($feed['projects'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>
        <div class="block community last">
            <h4><?php echo $this->text('feed-head-community'); ?></h4>
            <div class="item scroll-pane" style="height:350px;">
               <?php foreach ($feed['community'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>
    </div>

    <div class="see_more"><a href="/community/activity"><?php echo $this->text('regular-see_more') ?></a></div>
</div>
