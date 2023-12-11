<?php $channel=$this->channel; ?>

<footer id="footer" class="footer minimal-footer">
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
                <div class="spacer-20">
                    <?= $this->t('channel-call-footer-information') ?>
                <div class="row">
                    <ul>
                        <li class="col-md-2 col-sm-4 col-xs-4"><a href="/about" target="_blank"><?= $this->t('channel-call-footer-about') ?> </a></li>
                        <li class="col-md-2 col-sm-4 col-xs-4"><a href="/legal/terms" target="_blank"><?= $this->t('channel-call-footer-conditions') ?> </a></li>
                        <li class="col-md-2 col-sm-4 col-xs-4"><a href="/legal/privacy" target="_blank"><?= $this->t('channel-call-footer-privacy') ?> </a></li>
                        <li class="col-md-2 col-sm-4 col-xs-4"><a href="/about/librejs" target="_blank"><?= $this->t('channel-call-footer-licenses') ?> </a></li>
                        <li class="col-md-2 col-sm-4 col-xs-4"><a href="/channel/<?= $channel->id ?>/faq" target="_blank"><?= $this->t('channel-call-footer-faq') ?> </a></li>
                        <li class="col-md-2 col-sm-4 col-xs-4"><a href="/contact" target="_blank"><?= $this->t('channel-call-footer-contact') ?> </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
