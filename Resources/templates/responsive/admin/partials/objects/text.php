<?php

$value = $this->raw('value');
$ob = $this->raw('ob');

$link = $ob->getLink();

if(is_array($this->value)) $value = implode(', ', $value);

$class = $this->class ?: 'text';

if($link): ?>
    <a href="<?= $link ?>" class="pronto <?= $class ?>"><?= $value ?></a>
<?php else: ?>
    <span class="<?= $class ?>"><?= $value ?></span>
<?php endif ?>
