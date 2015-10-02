<textarea name="<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */ echo htmlspecialchars($this['name']) ?>" id="<?php echo htmlspecialchars($this['name']) ?>_editor" <?php

if ($this['cols'] > 0) echo ' cols="' . ((int) $this['cols']) . '"';

if ($this['rows'] > 0) echo ' rows="' . ((int) $this['rows']) . '"';

if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"';

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

?>><?php if (isset($this['value'])) echo $this['value'] ?></textarea>
