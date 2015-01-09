<input id="<?php echo htmlspecialchars($this['id']) ?>" name="<?php echo htmlspecialchars($this['name']) ?>" type="text"<?php if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"'?>  value="<?php if (isset($this['value'])) echo htmlspecialchars($this['value']) ?>"<?php if (isset($this['size'])) echo ' size="' . ((int) $this['size']) . '"' ?><?php if ($this['maxlength'] > 0) echo ' maxlength="' . ((int) $this['maxlength']) . '"' ?> />
<?php
if (isset($this['symbol'])) echo '<span class="symbol">'.$this['symbol'].'</span>';
/* ya no hace falta
<script type="text/javascript">
<?php include __DIR__ . '/textbox.js.src.php' ?>
</script>
*/