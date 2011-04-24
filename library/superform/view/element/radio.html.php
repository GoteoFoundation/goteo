<label>

    <?php if (isset($this['label'])): ?>
    <strong><?php echo htmlspecialchars($this['label']) ?></strong>
    <?php endif ?>

    <?php if (isset($this['summary'])): ?>
    <p class="summary"><?php echo htmlspecialchars($this['summary']) ?></p>
    <?php endif ?>
    
    <input type="radio" name="<?php echo htmlspecialchars($this['name']) ?>" value="<?php echo htmlspecialchars($this['value']) ?>"<?php if ($this['checked']) echo 'checked="checked"' ?> />
    
</label>