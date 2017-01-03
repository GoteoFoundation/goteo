<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\Feed;

$items = $vars['items'];
?>
<div class="widget feed">
    <script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
        jQuery(document).ready(function($) {
            $('.scroll-pane').jScrollPane({showArrows: true});
        });
    // @license-end
    </script>
    <h3 class="title"><?php echo Text::get('dashboard-menu-activity-wall'); ?></h3>

    <div style="height:auto;overflow:auto;margin-left:15px">

        <div class="block goteo">
           <h4>Cofinancio</h4>
           <div class="item scroll-pane" style="height:800px;">
               <?php foreach ($items['supported'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>

        <div class="block projects">
            <h4>Participo</h4>
            <div class="item scroll-pane" style="height:800px;">
               <?php foreach ($items['comented'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>
        <div class="block community last">
            <h4>Notificaciones</h4>
            <div class="item scroll-pane" style="height:800px;">
               <?php foreach ($items['private'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>
    </div>
</div>
