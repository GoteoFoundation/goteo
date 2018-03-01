<?php

$value = $this->raw('value');
$ob = $this->raw('ob');

$link = $this->link ? $this->link : $ob->getLink('admin');

if(is_array($this->value)) $value = implode(', ', $value);

$class = $this->class ?: 'text';

if($link): ?>
    <a href="<?= $link ?>" class="<?= $class ?>"><?= $value ?></a>
<?php else: ?>
    <span class="<?= $class ?>"><?= $value ?></span>
<?php endif ?>
