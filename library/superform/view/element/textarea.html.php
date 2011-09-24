<textarea <?php if ($this['cols'] > 0) echo ' cols="' . ((int) $this['cols']) . '"' ?><?php if ($this['rows'] > 0) echo ' rows="' . ((int) $this['rows']) . '"' ?>name="<?php echo htmlspecialchars($this['name']) ?>" id="<?php echo htmlspecialchars($this['name']) ?>_editor"<?php if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"'?>><?php if (isset($this['value'])) echo $this['value'] ?></textarea>
<script type="text/javascript">
<?php include __DIR__ . '/textarea.js.src.php' ?>
</script>