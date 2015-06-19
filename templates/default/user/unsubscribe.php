<?php

$error = $this->error;
$message = $this->message;

$this->layout('layout', [
    'bodyClass' => 'user-login',
    ]);

$this->section('content');

?>
    <div id="main">

        <div class="login">

            <div>

                <?php if ($error): ?>
                <p class="error"><?= $error ?></p>
                <?php endif ?>
                <?php if ($message): ?>
                <p><?= $message ?></p>
                <?php endif ?>

            </div>
        </div>

    </div>

<?php $this->replace() ?>

