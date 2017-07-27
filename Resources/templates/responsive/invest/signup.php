<?php

$this->layout('invest/layout', [
    'alt_title' => $this->text('meta-title-register')
    ]);

$this->section('main-content');

$query = $this->query . '&amp;return=' . urlencode($this->raw('return')) . '&amp;lang=' . $this->lang_current();

?>

<div class="container">
    <div class="row row-form">
        <div class="panel panel-default invest-container">
            <div class="panel-body">

                <h2 class="col-md-offset-1">
                    <?= $this->text('register-form-title') ?>
                    <small>| <a href="<?= '/invest/' . $this->project->id . '/login?' . $query ?>"><?= $this->text('register-question') ?></a></small>
                </h2>
                <?php if($this->skip_login): ?>
                <p class="col-md-offset-1 padding-bottom-6">
                <a href="/invest/<?= $this->project->id ?>/payment?<?= $query ?>&email" ><i class="fa fa-sign-out"></i> <?= $this->text('invest-no-register') ?></a>
                </p>
                <?php endif ?>


                <form class="form-horizontal" role="form" method="post" action="/invest/<?= $this->project->id ?>/signup?<?= $query ?>">

                <?= $this->insert('auth/partials/form_signup') ?>

                <?= $this->insert('auth/partials/social_login') ?>

                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->replace() ?>

<?php $this->section('content') ?>
    <?= $this->insert('auth/partials/openid_modal') ?>
<?php $this->append() ?>

<?php $this->section('footer') ?>
    <?= $this->insert('auth/partials/javascript_signup') ?>
<?php $this->append() ?>
