<input type="submit" name="<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */ echo htmlspecialchars($this['name']) ?>" value="<?php echo htmlspecialchars($this['label']) ?>"<?php

if (!empty($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"';

if (!empty($this['disabled'])) echo ' disabled="' . htmlspecialchars($this['disabled']) . '"';

if (!empty($this['onclick'])) echo ' onclick="' . addcslashes($this['onclick'], '"') . '"';

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

?> />
