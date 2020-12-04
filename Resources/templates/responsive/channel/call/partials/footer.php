<?php $channel=$this->channel; ?>

<footer id="footer" class="footer">
    <div class="container-fluid">
      <div class="container">
        <div class="row header">
          <div class="pull-left">
            <a href="<?= '/channel/'.$this->channel->id ?> ">
              <img src="<?= $channel->logo_footer ? $channel->logo_footer->getlink(0,50) : '' ?>" height="50">
            </a>
          </div>
          <div class="pull-right">
            <span><?= $this->text('call-header-powered-by') ?></span>
            <a href="<?= $this->get_config('url.main') ?>">
              <img src="<?= '/assets/img/goteo-blue-green.svg' ?>" >
            </a>
          </div>
        </div>
        <div class="row spacer-20">
          <div class="col-md-6 col-sm-8">
            <div class="spacer-20">
              <?= $this->t('channel-call-footer-information') ?>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-6 col-xs-6">
                <ul>
                  <li><a href="/about" target="_blank"><?= $this->t('channel-call-footer-about') ?> </a></li>
                  <li><a href="https://stats.goteo.org" target="_blank"><?= $this->t('channel-call-footer-stats') ?> </a></li>
                  <li><a href="/legal/terms" target="_blank"><?= $this->t('channel-call-footer-conditions') ?> </a></li>
                  <li><a href="/legal/privacy" target="_blank"><?= $this->t('channel-call-footer-privacy') ?> </a></li>
                </ul>
              </div>
              <div class="col-md-6 col-sm-6 col-xs-6">
                <ul>
                  <li><a href="/about/librejs" target="_blank"><?= $this->t('channel-call-footer-licenses') ?> </a></li>
                  <li><a href="/faq" target="_blank"><?= $this->t('channel-call-footer-faq') ?> </a></li>
                  <li><a href="/contact" target="_blank"><?= $this->t('channel-call-footer-contact') ?> </a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-4">
            <div class="spacer-20">
              <?= $this->t('channel-call-footer-social-networks') ?>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12 col-xs-6">
                <ul>
                  <li><a href="<?=$this->text('social-account-twitter') ?>" target="_blank"><span class="icon icon-channel-twitter"></span> <?= $this->t('channel-call-footer-twitter') ?> </a></li>
                  <li><a href="<?=$this->text('social-account-facebook') ?>" target="_blank"><span class="icon icon-channel-fb"></span> <?= $this->t('channel-call-footer-facebook') ?> </a></li>
                  <li><a href="<?=$this->text('social-account-instagram') ?>" target="_blank"><span class="icon icon-channel-instagram"></span> <?= $this->t('channel-call-footer-instagram') ?> </a></li>
                </ul>
              </div>
              <div class="col-md-6 col-sm-12 col-xs-6">
              <ul>
                  <li><a href="<?=$this->text('social-account-telegram') ?>" target="_blank"><span class="icon icon-channel-telegram"></span> <?= $this->t('channel-call-footer-telegram') ?> </a></li>
                  <li><a href="<?=$this->text('social-account-github') ?>" target="_blank"><span class="icon icon-channel-github"></span> <?= $this->t('channel-call-footer-github') ?> </a></li>
                  <li><a href="/newsletter" target="_blank"><span class="icon icon-channel-mail"></span> <?= $this->t('channel-call-footer-newsletter') ?> </a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</footer>