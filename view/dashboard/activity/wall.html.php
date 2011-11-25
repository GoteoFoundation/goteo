<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$items = $this['items'];
?>
<div class="widget feed">
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.scroll-pane').jScrollPane({showArrows: true});

            $('.hov').hover(
              function () {
                $(this).addClass($(this).attr('rel'));
              },
              function () {
                $(this).removeClass($(this).attr('rel'));
              }
            );

        });
        </script>
        <h2 class="title"><?php echo Text::get('dashboard-menu-activity-wall'); ?></h2>

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

</div>
