<?php if (count($this) > 0): ?>
<div class="elements">
    <ol>
        <?php foreach ($this as $element): ?>
        <!-- SFEL#<?php echo $element->id ?> -->
        <li class="element<?php echo rtrim(' ' . strtolower(htmlspecialchars($element->type))) .  rtrim(' ' . htmlspecialchars($element->class)) ?><?php if ($element->required) echo ' required' ?><?php if ($element->ok) echo ' ok' ?><?php if (!empty($element->errors)) echo ' error' ?>" id="li-<?php echo htmlspecialchars($element->id) ?>">
            <?php echo $element->render() ?>
        </li>
        <!-- /SFEL#<?php echo $element->id ?> -->
        <?php endforeach ?>
    </ol>
</div>
<?php endif ?>
