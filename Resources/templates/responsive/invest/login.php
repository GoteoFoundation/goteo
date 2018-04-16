<?php

$this->layout('invest/layout', [
    'alt_title' => $this->text('login-title')
    ]);

$this->section('main-content');

$query = $this->query . '&amp;return=' . urlencode($this->raw('return')) . '&amp;lang=' . $this->lang_current();

?>

<div class="container">
    <div class="row row-form">
        <div class="panel panel-default panel-form">
            <div class="panel-body">

              <div style="padding:0 8%">
                <h2 class="col-md-offset-1"><?= $this->text('login-title') ?></h2>

                <form role="form" method="POST" action="/invest/<?= $this->project->id ?>/login?<?= $query ?>">

                <?= $this->insert('auth/partials/form_login', ['signup_link' => '/invest/' . $this->project->id . '/signup?' . $query]) ?>

                <?= $this->insert('auth/partials/social_login') ?>

                <?php if($this->skip_login): ?>
                <div class="form-group">
                    <div class="col-md-10 col-md-offset-1">
                        <a href="/invest/<?= $this->project->id ?>/payment?<?= $query ?>&email" ><i class="fa fa-sign-out"></i> <?= $this->text('invest-no-register') ?></a>
                    </div>
                </div>
                <?php endif ?>
                </form>
              </div>

            </div>
        </div>
    </div>
</div>

<?php $this->replace() ?>

<?php $this->section('content') ?>
    <?= $this->insert('auth/partials/recover_modal') ?>

    <?= $this->insert('auth/partials/openid_modal') ?>
<?php $this->append() ?>


<?php $this->section('footer') ?>
    <?= $this->insert('auth/partials/javascript_login') ?>
<?php $this->append() ?>
