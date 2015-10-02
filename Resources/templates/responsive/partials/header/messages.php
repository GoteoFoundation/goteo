<?php

$messages = $this->get_messages();
$errors = $this->get_errors();

if(!$messages && !$errors) return '';
?>
<div class="container">
<div class="row">
<?php
if($messages):
?>
    <div class="alert alert-success" role="alert">
        <span class="ui-icon ui-icon-info">&nbsp;</span>
        <?php foreach($messages as $message): ?>
            <p><?= nl2br($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php
endif;

if($errors):
?>
    <div class="alert alert-danger" role="alert">
        <span class="ui-icon ui-icon-error">&nbsp;</span>
        <?php foreach($errors as $message): ?>
            <p><?= nl2br($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif ?>
</div>
</div>
