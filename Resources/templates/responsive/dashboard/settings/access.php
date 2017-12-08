<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <h1><?= $this->text('dashboard-menu-profile-access')?></h1>
    <p><?= $this->text('guide-dashboard-user-access') ?></p>

    <div class="panel section-content spacer">
      <div class="panel-body">
            <h5><i class="fa fa-user"></i> <?= $this->text('login-access-username-field') ?></h5>
            <p><?= $this->user_id ?></p>

            <h5 class="spacer-20"><i class="fa fa-envelope"></i> <?= $this->text('login-register-email-field') ?></h5>
            <p><?= $this->user_email ?></p>


        <div class="spacer-20 forms">
            <p class="buttons">
                <button class="show-form btn btn-cyan btn-lg" data-target="#form1"><i class="fa fa-envelope-o"></i> <?= $this->text('user-changeemail-title') ?></button>
                <button class="show-form btn btn-cyan btn-lg" data-target="#form2"><i class="fa fa-key"></i> <?= $this->text('user-changepass-title') ?></button>
            </p>

            <blockquote id="form1" class="hidden">
                <?= $this->form_form($this->raw('form1')) ?>
                <button class="pull-right-form hide-form btn btn-default btn-lg" data-target="#form1"><i class="fa fa-ban"></i> <?= $this->text('regular-cancel') ?></button>
            </blockquote>

            <blockquote id="form2" class="hidden">
                <?= $this->form_form($this->raw('form2')) ?>
                <button class="pull-right-form hide-form btn btn-default btn-lg" data-target="#form2"><i class="fa fa-ban"></i> <?= $this->text('regular-cancel') ?></button>
            </blockquote>
        </div>


        <p class="spacer"><a href="/user/leave?email=<?= $this->get_user()->email ?>"><i class="fa fa-ban"></i> <?= $this->text('login-leave-header') ?></a></p>
      </div>
    </div>

  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    $(function(){
        $('.show-form').on('click', function(e){
            e.preventDefault();
            var $form = $($(this).data('target'));
            var $btns = $('.buttons');
            $btns.addClass('hidden');
            $form.removeClass('hidden').animateCss('foldInUp');
        });
        $('.hide-form').on('click', function(e){
            e.preventDefault();
            var $form = $($(this).data('target'));
            var $btns = $('.buttons');
            $form.animateCss('foldOutUp', function() {
                $form.addClass('hidden');
                $btns.removeClass('hidden');
            });
        });
    });
</script>
<?php $this->append() ?>
