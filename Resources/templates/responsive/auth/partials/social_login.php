<div class="form-group margin-top-7">
  <?php if($this->get_config('oauth.facebook.active')): ?>
    <div class="">
    <a href="/login/facebook?return=<?= urlencode($this->raw('return')) ?>" class="btn btn-block btn-social btn-facebook">
        <i class="fa fa-facebook"></i> <?= $this->text('login-signin-facebook') ?>
        </a>
    </div>
  <?php endif ?>

    <div class="standard-margin-top">

      <?php if($this->get_config('oauth.twitter.active')): ?>
        <a href="/login/twitter?return=<?= urlencode($this->raw('return')) ?>" class="btn btn-social-icon btn-twitter">
        <i class="fa fa-twitter"></i>
        </a>
      <?php endif ?>

      <?php if($this->get_config('oauth.google.active')): ?>
        <a href="/login/google?return=<?= urlencode($this->raw('return')) ?>" class="btn btn-social-icon btn-google">
        <i class="fa fa-google-plus"></i>
        </a>
      <?php endif ?>

      <?php if($this->get_config('oauth.yahoo.active')): ?>
        <a href="/login/Yahoo?return=<?= urlencode($this->raw('return')) ?>" class="btn btn-social-icon btn-yahoo">
        <i class="fa fa-yahoo"></i>
        </a>
      <?php endif ?>

      <?php if($this->get_config('oauth.linkedin.active')): ?>
        <a href="/login/linkedin?return=<?= urlencode($this->raw('return')) ?>" class="btn btn-social-icon btn-linkedin">
        <i class="fa fa-linkedin"></i>
        </a>
      <?php endif ?>

      <?php if($this->get_config('oauth.openid.active')): ?>
        <a href="" data-toggle="modal" data-target="#openIdModal" class="btn btn-social-icon btn-openid">
        <i class="fa fa-openid"></i>
        </a>
      <?php endif ?>

    </div>
</div>
