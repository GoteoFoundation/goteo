<?php if (isset($this['label'])) echo '<label>' ?><input type="checkbox" id="<?php echo htmlspecialchars($this['id']) ?>" name="<?php echo htmlspecialchars($this['name']) ?>" value="<?php echo htmlspecialchars($this['value']) ?>"<?php if ($this['checked']) echo ' checked="checked"' ?> />
<?php if (isset($this['label'])) echo htmlspecialchars($this['label']) . '</label>' ?>
<script type="text/javascript">
<?php include __DIR__ . '/checkbox.js.src.php' ?>
</script>