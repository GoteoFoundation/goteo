<?php

$worthcracy = $this['worthcracy'];

switch ($this['type']) {
    case 'main': // grande en contenido general
        $style = ' style="width:80px;float:left;"';
        break;
    case 'side': // pequeño al pie del lateral de cofinanciadores
        $style = ' style="float:left;"';
        break;
    default:
        $style = '';
        break;
}

// level: nivel que hay que resaltar con el "soy"
// , en este caso el resto de niveles por encima del destacado son grises



?>
<div class="widget project-summary">
    <?php foreach ($worthcracy as $level=>$worth) : ?>
        <div class="level worth-<?php echo $level; ?>"<?php echo $style; ?>>
            <?php echo '+ de ' . $worth->amount; ?><br />
            <?php if ($level == $this['level']) : ?>
            <strong><?php echo strtoupper("Soy {$worth->name}"); ?></strong>
            <?php else : ?>
            <span><?php echo $worth->name; ?></span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <div<?php echo $style; ?>>&euro;</div>
</div>