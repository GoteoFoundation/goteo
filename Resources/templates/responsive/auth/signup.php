<?php

$this->layout('auth/layout', [
    'title' => $this->text('meta-title-register'),
    'description' => $this->text('meta-title-register')
]);

$login_link = $this->login_link ? $this->raw('login_link') : ('/login?return=' . urlencode($this->raw('return')) );

$this->section('inner-content');
?>
  <div style="padding:0 8%">
    <h2 class="col-md-offset-1 padding-bottom-6">
        <?= $this->text('register-form-title') ?>
        <small>| <a href="<?= $login_link ?>"><?= $this->text('register-question') ?></a></small>
    </h2>

    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

    <form role="form" method="post" action="/signup?return=<?= urlencode($this->raw('return')) ?>">

    <?= $this->insert('auth/partials/form_signup') ?>

    <?= $this->insert('auth/partials/social_login') ?>

    </form>
  </div>
<?php $this->replace() ?>


<?php $this->section('content') ?>
    <?= $this->insert('auth/partials/openid_modal') ?>
<?php $this->append() ?>

<?php $this->section('footer') ?>
    <?= $this->insert('auth/partials/javascript_signup') ?>
<?php $this->append() ?>
