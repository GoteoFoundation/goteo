<?php

$value = $this->raw('value');

$link = $this->link;

if(is_array($this->value)) $value = implode(', ', $value);

$class = $this->class ?: 'text';
if($this->last) $class .= ' last';

if($link): ?>
    <a href="<?= $link ?>" class="<?= $class ?>"><?= $value ?></a>
<?php else: ?>
    <span class="<?= $class ?>"><?= $value ?></span>
<?php endif ?>
