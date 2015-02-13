<input type="submit" name="<?php echo htmlspecialchars($this['name']) ?>" value="<?php echo htmlspecialchars($this['label']) ?>"<?php

if (!empty($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"';

if (!empty($this['disabled'])) echo ' disabled="' . htmlspecialchars($this['disabled']) . '"';

if (!empty($this['onclick'])) echo ' onclick="' . addcslashes($this['onclick'], '"') . '"';

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

?> />
