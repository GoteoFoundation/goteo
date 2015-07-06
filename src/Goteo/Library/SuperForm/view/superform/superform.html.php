<div class="superform <?php echo $this['autoupdate'] ? ' autoupdate' : '' ?><?php if (isset($this['class'])) echo ' '. htmlspecialchars($this['class']) ?>"<?php if (isset($this['id'])) echo ' id="'. htmlspecialchars($this['id']) . '"' ?>>

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
