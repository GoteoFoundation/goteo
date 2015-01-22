<input id="<?php echo htmlspecialchars($this['id']) ?>" name="<?php echo htmlspecialchars($this['name']) ?>" type="text" value="<?php if (isset($this['value'])) echo htmlspecialchars($this['value']) ?>"<?php

if (isset($this['size'])) echo ' size="' . ((int) $this['size']) . '"';

if ($this['maxlength'] > 0) echo ' maxlength="' . ((int) $this['maxlength']) . '"';

if (isset($this['class'])) echo ' class="' . htmlspecialchars($this['class']) . '"';

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

 ?> />
<?php
if (isset($this['symbol'])) echo '<span class="symbol">'.$this['symbol'].'</span>';
