<?php
use Goteo\Library\Feed,
    Goteo\Library\Text;

$feed = $vars['feed'];

?>
<div class="widget feed">
    <script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    jQuery(document).ready(function($) {
        $('.scroll-pane').jScrollPane({showArrows: true});
    });
    // @license-end
    </script>
    <h3 class="title"><?php echo Text::get('feed-header'); ?></h3>

    <div style="height:auto;overflow:auto;margin-left:15px">

        <div class="block goteo">
           <h4><?php echo Text::get('feed-head-goteo'); ?></h4>
           <div class="item scroll-pane" style="height:350px;">
               <?php foreach ($feed['goteo'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>

        <div class="block projects">
            <h4><?php echo Text::get('feed-head-projects'); ?></h4>
            <div class="item scroll-pane" style="height:350px;">
               <?php foreach ($feed['projects'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>
        <div class="block community last">
            <h4><?php echo Text::get('feed-head-community'); ?></h4>
            <div class="item scroll-pane" style="height:350px;">
               <?php foreach ($feed['community'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>
    </div>

    <div class="see_more"><a href="/community/activity"><?php echo Text::get('regular-see_more') ?></a></div>
</div>
