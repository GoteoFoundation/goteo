<?php
use Goteo\Library\Page,
    Goteo\Library\Feed;

$items = $this['items'];

?>
<div class="widget feed">
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.scroll-pane').jScrollPane({showArrows: true});
    });
    </script>
    <h3 class="title">Actividad reciente</h3>

    <div style="height:auto;overflow:auto;margin-left:15px">

        <div class="block goteo">
           <h4>Goteo</h4>
           <div class="item scroll-pane" style="height:600px;">
               <?php foreach ($items['goteo'] as $item) : 
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>

        <div class="block projects">
            <h4>Proyectos</h4>
            <div class="item scroll-pane" style="height:600px;">
               <?php foreach ($items['projects'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>
        <div class="block community last">
            <h4>Comunidad</h4>
            <div class="item scroll-pane" style="height:600px;">
               <?php foreach ($items['community'] as $item) :
                   echo Feed::subItem($item);
                endforeach; ?>
           </div>
        </div>
    </div>
</div>
