<?php

if($this->return) $return = $this->return;
else              $return = urlencode($this->get_query('return'));

?>    <div class="form-group margin-top-7">
        <div class="col-md-10 col-md-offset-1">
            <a href="/login/facebook?return=<?= $return ?>" class="btn btn-block btn-social btn-facebook">
                <i class="fa fa-facebook"></i> <?= $this->text('login-signin-facebook') ?>
                </a>
            </div>

            <div class="col-md-10 col-md-offset-1 standard-margin-top">
                <a href="/login/twitter?return=<?= $return ?>" class="btn btn-social-icon btn-twitter">
                <i class="fa fa-twitter"></i>
                </a>
                <a href="/login/google?return=<?= $return ?>" class="btn btn-social-icon btn-google">
                <i class="fa fa-google-plus"></i>
                </a>

                <a href="/login/Yahoo?return=<?= $return ?>" class="btn btn-social-icon btn-yahoo">
                <i class="fa fa-yahoo"></i>
                </a>
                <a href="/login/linkedin?return=<?= $return ?>" class="btn btn-social-icon btn-linkedin">
                <i class="fa fa-linkedin"></i>
                </a>
                <a href="/login/facebook?return=<?= $return ?>" class="btn btn-social-icon btn-openid">
                <i class="fa fa-openid"></i>
            </a>
            </div>
        </div>
