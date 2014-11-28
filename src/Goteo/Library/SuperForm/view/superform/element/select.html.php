<select name="<?php echo htmlspecialchars($this['name']) ?>" id="<?php echo htmlspecialchars($this['name']) ?>_editor"<?php if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"'?>>
    <?php foreach ($this['options'] as $option): ?>
    <option value="<?php echo $option['value'] ?>"<?php if ($option['value'] == $this['value']) echo ' selected="selected"' ?>><?php echo $option['label'] ?></option>
    <?php endforeach ?>
</select>
<?php
/* ya no hace falta
<script type="text/javascript">
<?php include __DIR__ . '/select.js.src.php' ?>
</script>
*/