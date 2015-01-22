<textarea name="<?php echo htmlspecialchars($this['name']) ?>" id="<?php echo htmlspecialchars($this['name']) ?>_editor" <?php

if ($this['cols'] > 0) echo ' cols="' . ((int) $this['cols']) . '"';

if ($this['rows'] > 0) echo ' rows="' . ((int) $this['rows']) . '"';

if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"';

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

?>><?php if (isset($this['value'])) echo $this['value'] ?></textarea>
