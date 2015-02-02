<input id="<?php echo htmlspecialchars($this['id']) ?>" name="<?php echo htmlspecialchars($this['name']) ?>" type="password"<?php

if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"';

if (isset($this['size'])) echo ' size="' . ((int) $this['size']) . '"';

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

?> value="<?php if (isset($this['value'])) echo htmlspecialchars($this['value']) ?>" />
