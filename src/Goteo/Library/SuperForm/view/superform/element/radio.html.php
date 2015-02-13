<?php
use Goteo\Core\View;
?>
<label>

    <?php if (isset($this['label'])): ?>
    <?php echo htmlspecialchars($this['label']) ?>
    <?php endif ?>

    <input id="<?php echo htmlspecialchars($this['id']) ?>" type="radio" name="<?php echo htmlspecialchars($this['name']) ?>" value="<?php echo htmlspecialchars($this['value']) ?>"<?php if ($this['checked']) echo ' checked="checked"' ?> />

</label>

<?php
if (!empty($this['children'])): ?>
    <div class="<?php if (!$this['checked']) echo 'jshidden ' ?>children" id="<?php echo htmlspecialchars($this['id']) ?>-children">
        <?php echo View::get('superform/elements.html.php', Goteo\Library\SuperForm::getChildren($this['children'], $this['level'])) ?>
    </div>
<?php

endif;
