<?php

use Goteo\Library\Worth;
use Goteo\Application\Currency;

$worthcracy = isset($vars['worthcracy']) ? $vars['worthcracy'] : Worth::getAll();

$select_currency=Currency::$currencies[$_SESSION['currency']]['html'];

if (!isset($vars['level'])) $vars['level'] = 9999;

// level: nivel que hay que resaltar con el "soy"
// , en este caso el resto de niveles por encima del destacado son grises

?>
<ul class="worthcracy">
<?php foreach ($worthcracy as $level => $worth): ?>
<li class="worth-<?php echo $level ?><?php if ($level <= $vars['level']) echo ' done' ?>">
    <span class="threshold">+ de <strong><?php echo \amount_format($worth->amount, 0, true); ?></strong></span>
    <?php if ($level == $vars['level']) : ?>
    <strong class="name"><?php echo htmlspecialchars($worth->name) ?></strong>
    <?php else: ?>
    <em class="name"><?php echo htmlspecialchars($worth->name) ?></em>
    <?php endif; ?>
</li>
<?php endforeach ?>
</ul>
<!-- aqui va la moneda en session -->
<span class="symbol" ><?php echo $select_currency; ?></span>
