<ul>
    <?php foreach ($this['options'] as $radio): ?>
    <li<?php if (isset($radio->class)) echo ' class="' . htmlspecialchars($radio->class) . '"' ?>><?php echo $radio->getInnerHTML() ?></li>
    <?php endforeach ?>
</ul>