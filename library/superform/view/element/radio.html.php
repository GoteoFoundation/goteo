<label>

    <?php if (isset($this['label'])): ?>
    <?php echo htmlspecialchars($this['label']) ?>
    <?php endif ?>
    
    <input type="radio" name="<?php echo htmlspecialchars($this['name']) ?>" value="<?php echo htmlspecialchars($this['value']) ?>"<?php if ($this['checked']) echo 'checked="checked"' ?> />
    
</label>