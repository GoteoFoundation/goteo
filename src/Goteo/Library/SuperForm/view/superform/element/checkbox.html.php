<?php if (isset($this['label'])) echo '<label>' ?><input type="checkbox" id="<?php echo htmlspecialchars($this['id']) ?>" name="<?php echo htmlspecialchars($this['name']) ?>" value="<?php echo htmlspecialchars($this['value']) ?>"<?php

if ($this['checked']) echo ' checked="checked"';

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

?> />
<?php if (isset($this['label'])) echo htmlspecialchars($this['label']) . '</label>' ?>
