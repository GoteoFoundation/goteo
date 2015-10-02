<select name="<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */ echo htmlspecialchars($this['name']) ?>" id="<?php echo htmlspecialchars($this['name']) ?>_editor"<?php

if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"';

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}


?>>
<?php foreach ($this['options'] as $option): ?>
    <option value="<?php echo $option['value'] ?>"<?php if ($option['value'] == $this['value']) echo ' selected="selected"' ?>><?php echo $option['label'] ?></option>
<?php endforeach ?>

</select>
