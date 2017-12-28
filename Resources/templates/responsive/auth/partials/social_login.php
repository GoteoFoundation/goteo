<?php
$providers = ['facebook', 'twitter', 'google', 'yahoo', 'linkedin', 'openid'];
$goteo_oauth_provider = $this->get_cookie('goteo_oauth_provider');
if($goteo_oauth_provider) {
    $providers = array_diff($providers, [$goteo_oauth_provider]);
    array_unshift($providers, $goteo_oauth_provider);
}

?><div class="form-group margin-top-7">
    <?php foreach($providers as $i => $provider):
        if($this->get_config("oauth.$provider.active")):
            $url = "/login/$provider";
            if($provider === 'openid') $url = '#openIdModal" data-toggle="modal"';
            if($provider === 'Yahoo') $url = "/login/Yahoo";
            if($url) $url .= "?return=" . urlencode($this->raw('return'));
            $text = $this->text("login-signin-$provider");
            if($i === 0):
    ?>
            <div>
              <a href="<?= $url ?>" class="btn btn-block btn-social btn-<?= $provider ?>">
                <i class="fa fa-<?= $provider ?>"></i> <?= $text ?>
              </a>
            </div>

            <div class="standard-margin-top">
          <?php else: ?>
              <a href="<?= $url ?>" title="<?= $text ?>" class="btn btn-social-icon btn-<?= $provider ?>">
                <i class="fa fa-<?= $provider ?>"></i>
              </a>
          <?php endif ?>
      <?php endif ?>
    <?php endforeach ?>
            </div>
    </div>
</div>
