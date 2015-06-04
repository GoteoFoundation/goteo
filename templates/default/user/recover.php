<?php

// para que el prologue ponga el código js para botón facebook en el bannerside
$fbCode = $this->text_widget($this->text('social-account-facebook'), 'fb');

$this->layout('layout', [
    'bodyClass' => 'user-login',
    ]);

$this->section('content');

?>
<div id="sub-header">
	<div class="clearfix">
		<div class="subhead-banner">
			<h2 class="message"><?= $this->text_html('login-banner-header') ?></h2>
		</div>
		<div class="mod-pojctopen"><?= $this->text_html('open-banner-header', $fbCode) ?></div>
	</div>
</div>
    <div id="main">

        <div class="login">

            <div>

                <h2><?= $this->text('login-recover-header') ?></h2>

            <?php if ($this->error): ?>
                <p class="error"><?= $this->error ?></p>
            <?php endif ?>

            <?php if ($this->message): ?>
                <p><?php echo $this->message ?></p>
            <?php endif ?>

                <form action="/user/recover" method="post">
                    <div class="email">
                        <label><?= $this->text('login-recover-email-field') ?>
                        <input type="text" name="email" value="<?= $this->email ?>" /></label>
                    </div>

                    <input type="submit" name="recover" value="<?= $this->text('login-recover-button') ?>" />

                </form>

            </div>
        </div>

    </div>

<?php $this->replace() ?>


