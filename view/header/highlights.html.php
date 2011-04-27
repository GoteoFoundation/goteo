<?php

$highlights = <<<END
Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
Phasellus mi turpis, pharetra ut luctus ac, imperdiet eu enim. 
Maecenas condimentum fringilla erat, non imperdiet quam faucibus sed. 
Cras velit libero, blandit sit amet facilisis eu, porttitor sed quam. 
Aliquam mauris justo, vehicula eu dignissim quis, tempor vitae purus. 
Vestibulum porttitor pretium lorem, sollicitudin rutrum mi adipiscing non. 
Ut viverra risus at erat mattis fermentum. 
Aliquam dictum libero venenatis nisl aliquam sit amet porttitor leo consequat. 
Duis auctor nunc hendrerit lacus vestibulum convallis. Sed lacus ante, aliquam quis luctus et, rhoncus in ipsum. 
Etiam imperdiet mauris non felis pharetra laoreet. 
Aenean eros dui, hendrerit id lobortis non, pharetra in mauris. 
Sed lobortis luctus ipsum. Integer sit amet tristique velit. 
Maecenas mi eros, rutrum sit amet pellentesque sed, dapibus nec arcu. 
Vestibulum posuere congue sapien, nec mattis risus pharetra vel. 
Suspendisse potenti. Morbi arcu quam, consectetur nec gravida id, aliquam eget augue.
END;

?>
<div id="highlights">
    
    <h2>Noticias</h2>
    
    <ul>
        <?php foreach (preg_split("/\n\s*/", $highlights) as $hl): ?>
        <li><?php echo htmlspecialchars($hl) ?> <a href="">Ver más</a></li>
        <?php endforeach ?>
    </ul>
    
</div>