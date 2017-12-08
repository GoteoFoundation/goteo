<?php

$this->layout('auth/layout', [
    'title' => $this->text('login-title'),
    'description' => $this->text('login-title')
    ]);

$this->section('inner-content');

?>
    <h2 class="col-md-offset-1 padding-bottom-6"><?= $this->text('login-title') ?></h2>

    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

    <form class="form-horizontal" role="form" method="POST" action="/login?return=<?= urlencode($this->raw('return')) ?>&amp;lang=<?= $this->lang_current() ?>">

    <?= $this->insert('auth/partials/form_login') ?>

    <?= $this->insert('auth/partials/social_login') ?>

    </form>

<?php $this->replace() ?>

<?php $this->section('content') ?>
    <?= $this->insert('auth/partials/recover_modal') ?>

    <?= $this->insert('auth/partials/openid_modal') ?>
<?php $this->append() ?>


<?php $this->section('footer') ?>
    <?= $this->insert('auth/partials/javascript_login') ?>
<?php $this->append() ?>
