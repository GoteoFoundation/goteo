<?php

$messages = $this->get_messages();
$errors = $this->get_errors();

if(!$messages && !$errors) return '';
?>

<div class="container-fluid">
<?php
if($messages):
?>
    <div class="alert alert-success spacer-20" role="alert">
        <a href="#" class="close custom-close col-md-offset-1" data-dismiss="alert" aria-label="close">&times;</a>
        <?php foreach($messages as $message): ?>
            <p><?= nl2br($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php
endif;

if($errors):
?>
    <div class="alert alert-danger spacer-20" role="alert">
        <a href="#" class="close custom-close col-md-offset-1" data-dismiss="alert" aria-label="close">&times;</a>
        <?php foreach($errors as $message): ?>
            <p><?= nl2br($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif ?>
</div>
