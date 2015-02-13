<input name="<?php echo htmlspecialchars($this['name']) ?>" type="file"<?php

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

?> /> <input type="submit" name="upload" value="<?php echo htmlspecialchars($this['label']) ?>"<?php

if (!empty($this['onclick'])) echo ' onclick="' . addcslashes($this['onclick'], '"') . '"';

?> />
