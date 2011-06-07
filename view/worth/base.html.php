<?php

$worthcracy = $this['worthcracy'];

// level: nivel que hay que resaltar con el "soy"
// , en este caso el resto de niveles por encima del destacado son grises

?>
<ul class="worthcracy">
<?php foreach ($worthcracy as $level => $worth): ?>
<li class="worth-<?php echo $level ?><?php if ($level <= $this['level']) echo ' done' ?>">
    <span class="threshold">+ de <strong><?php echo $worth->amount ?></strong> <span class="euro">&euro;</span> </span>        
    <?php if ($level == $this['level']) : ?>
    <strong class="name"><?php echo htmlspecialchars($worth->name) ?></strong>
    <?php else: ?>
    <em class="name"><?php echo htmlspecialchars($worth->name) ?></em>        
    <?php endif; ?>
</li>
<?php endforeach ?>
</ul>