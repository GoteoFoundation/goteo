<input name="<?php echo htmlspecialchars($this['name']) ?>" type="hidden" value="<?php if (isset($this['value'])) echo htmlspecialchars($this['value']) ?>"<?php

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

?> />
