<?php

$highlights = <<<END
Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
Phasellus mi turpis, pharetra ut luctus ac, imperdiet eu enim. 
Maecenas condimentum fringilla erat, non imperdiet quam faucibus sed. 
Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
Phasellus mi turpis, pharetra ut luctus ac, imperdiet eu enim. 
Maecenas condimentum fringilla erat, non imperdiet quam faucibus sed. 
Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
Phasellus mi turpis, pharetra ut luctus ac, imperdiet eu enim. 
Maecenas condimentum fringilla erat, non imperdiet quam faucibus sed. 
END;

?>
<div id="highlights">
    
    <h2><a href="">Noticias</a></h2>
    
    <ul>
        <?php foreach (preg_split("/\n\s*/", $highlights) as $i => $hl): ?>
        <li><?php echo htmlspecialchars($hl) ?> <a href="">Ver más</a></li>
        <?php endforeach ?>
    </ul>
    
    <script type="text/javascript">
        
    jQuery(document).ready(function ($) {
        
        var ul = $('#highlights > ul'),
            lis = ul.children('li'),
            li = lis.first(),
            stopped = false;
        
        setInterval(function () {            
            
            if (stopped) return;
            
            li = li.next();
            
            var m;
            
            if (!li.length) {
                li = lis.first();
                m = 0;
            } else {
                m = '-=' + Math.abs(li.position().top * -1);
            }
            
            ul.animate({
               'margin-top': m
            }, 400);
            
        }, 8000);
        
        ul.bind('mouseenter', function () {
            stopped = true;
            ul.bind('mouseleave', function () {
                stopped = false;
            });
        });
        
    });
    </script>
    
</div>