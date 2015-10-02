<div class="superform <?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */ echo $this['autoupdate'] ? ' autoupdate' : '' ?><?php if (isset($this['class'])) echo ' '. htmlspecialchars($this['class']) ?>"<?php if (isset($this['id'])) echo ' id="'. htmlspecialchars($this['id']) . '"' ?>>

    <?php if (isset($this['title'])): ?>
    <h<?php echo $this['level'] ?>><?php echo htmlspecialchars($this['title']) ?></h<?php echo $this['level'] ?>>
    <?php endif ?>

    <?php if (isset($this['hint'])): ?>
    <div class="hint">
        <blockquote><?php echo $this['hint'] ?></blockquote>
    </div>
    <?php endif ?>

    <?php echo \Goteo\Core\View::get('superform/elements.html.php', $this['elements']) ?>

    <?php if(!empty($this['footer'])): ?>
    <div class="footer">
        <div class="elements">
            <?php foreach ($this['footer'] as $element): ?>
            <div class="element">
                <?php echo $element->getInnerHTML() ?>
            </div>
            <?php endforeach ?>
        </div>
    </div>
    <?php endif ?>



</div>
