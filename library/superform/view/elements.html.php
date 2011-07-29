<?php if (count($this) > 0): ?>
<div class="elements">   
    <ol>
        <?php foreach ($this as $element): ?>
        <!-- SFEL#<?php echo $element->id ?> -->
        <li class="element<?php echo rtrim(' ' . htmlspecialchars($element->type)) .  rtrim(' ' . htmlspecialchars($element->class)) ?><?php if ($element->required) echo ' required' ?><?php if ($element->ok) echo ' ok' ?><?php if (!empty($element->errors)) echo ' error' ?>" id="<?php echo htmlspecialchars($element->id) ?>" name="<?php echo htmlspecialchars($element->id) ?>">
            <?php echo (string) $element ?>           
        </li>
        <!-- /SFEL#<?php echo $element->id ?> -->
        <?php endforeach ?>
    </ol>
</div>
<?php endif ?>