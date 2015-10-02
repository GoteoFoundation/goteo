<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */ if (count($this) > 0): ?>
<div class="elements">   
    <ol>
        <?php foreach ($this as $element): ?>
        <li class="element<?php echo rtrim(' ' . strtolower(htmlspecialchars($element->type))) .  rtrim(' ' . htmlspecialchars($element->class)) ?>" id="<?php echo htmlspecialchars($element->id) ?>" name="<?php echo htmlspecialchars($element->id) ?>">
            <?php echo (string) $element ?>           
        </li>
        <?php endforeach ?>
    </ol>
</div>
<?php endif ?>