<input id="<?php echo htmlspecialchars($this['id']) ?>" name="<?php echo htmlspecialchars($this['name']) ?>" type="text"<?php if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"'?>  value="<?php if (isset($this['value'])) echo htmlspecialchars($this['value']) ?>"<?php if (isset($this['size'])) echo 'size="' . ((int) $this['size']) . '"' ?><?php if ($this['maxlength'] > 0) echo 'maxlength="' . ((int) $this['maxlength']) . '"' ?> />
<script type="text/javascript">
<?php include __DIR__ . '/textbox.js.src.php' ?>
</script>