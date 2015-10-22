<?php

$messages = $this->get_messages();
$errors = $this->get_errors();

if(!$messages && !$errors) return '';
?>

<div class="row">
<?php
if($messages):
?>
    <div class="alert alert-success table-responsive" role="alert">
        <div class="col-md-10 col-md-offset-1">
            <a href="#" class="close custom-close col-md-offset-1" data-dismiss="alert" aria-label="close">&times;</a>
            <?php foreach($messages as $message): ?>
                <p><?= nl2br($message) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
<?php
endif;

if($errors):
?>
    <div class="alert alert-danger table-responsive" role="alert">
        <div class="col-md-10 col-md-offset-1">
            <a href="#" class="close custom-close col-md-offset-1" data-dismiss="alert" aria-label="close">&times;</a>
            <?php foreach($errors as $message): ?>
                <p><?= nl2br($message) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif ?>
</div>
