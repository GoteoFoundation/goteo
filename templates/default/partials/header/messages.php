<?php

$messages = $this->get_messages();
$errors = $this->get_errors();

if($messages):
?>
<div id="message" class="info">
    <div id="message-content">
        <input type="button" class="message-close" />
        <ul>
    <?php foreach($messages as $message): ?>
            <li>
                <span class="ui-icon ui-icon-info">&nbsp;</span>
                <span><?= nl2br($message) ?></span>
            </li>
    <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php
endif;

if($errors):
?>
<div id="message" class="error">
    <div id="message-content">
        <input type="button" class="message-close red-close" />
        <ul>
    <?php foreach($errors as $message): ?>
            <li>
                <span class="ui-icon ui-icon-error">&nbsp;</span>
                <span><?= nl2br($message) ?></span>
            </li>
    <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php

endif;
