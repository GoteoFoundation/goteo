<?php
$style_hidden = "width: 0px; height: 0px; margin: 0px; padding: 0px; border: 0px; opacity: 0; display: block;"
?>

<label for="<?= $this->trap ?>" style="<?= $style_hidden ?>">
    <?= $this->text('contact-email-field') ?>
</label>
<br style="<?= $style_hidden ?>" />
<input id="<?= $this->trap ?>" name="<?= $this->trap ?>" value="" type="text" class="short" style="<?= $style_hidden ?>" />