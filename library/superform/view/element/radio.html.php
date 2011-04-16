<?php if (isset($this['label'])) echo '<label>' ?><input type="radio" name="<?php echo htmlspecialchars($this['name']) ?>" value="<?php echo htmlspecialchars($this['value']) ?>"<?php if ($this['checked']) echo 'checked="checked"' ?> />
<?php if (isset($this['label'])) echo htmlspecialchars($this['label']) . '</label>' ?>
