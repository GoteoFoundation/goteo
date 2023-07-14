<footer id="footer" class="footer">
    <div class="container-fluid">
      <div class="container">
        <div class="row">
            <div class="col-sm-8">
              <ul class="list-inline footer-list">
                <li><a href="<?= $this->lang_host() ?>blog"><?= $this->text('regular-header-about')?></a></li>
                <li><a href="https://stats.goteo.org" target="_blank"><?= $this->text('footer-resources-stats') ?></a></li>
                <li><a href="<?= $this->lang_host() ?>legal/terms"><?= $this->text('regular-footer-terms')?></a></li>
                <li><a href="<?= $this->lang_host() ?>legal/privacy"><?= $this->text('regular-footer-privacy')?></a></li>
                <li><a data-jslicense="1" href="<?= $this->lang_host() ?>about/librejs">Licenses</a></li>
                <li><a href="<?= $this->lang_host() ?>faq"><?= $this->text('regular-header-faq')?></a></li>
                <li><a href="<?= $this->lang_host() ?>contact"><?= $this->text('regular-footer-contact')?></a></li>
              </ul>
            </div>

            <div class="col-sm-4 hidden-xs">
              <ul class="list-inline text-right">
                <li class="label-img"><a href="#"><?=$this->text('footer-platoniq-iniciative') ?></a></li>
                <li><a href="<?= $this->lang_host() ?>"><img src="<?= $this->asset('img/fundacion-platoniq-white.png') ?>" class="img-responsive logo" alt="<?= $this->t('footer-platoniq-foundation') ?>" width="100"></a></li>
              </ul>
            </div>
        </div>
        <ul class="social text-center list-inline footer-list">
          <li><a class="fa fa-twitter" target="_blank" href="<?=$this->text('social-account-twitter') ?>" title="<?= $this->t('regular-share-twitter')?>"></a></li>
          <li><a class="fa fa-facebook" target="_blank" href="<?=$this->text('social-account-facebook') ?>" title="<?= $this->t('regular-share-facebook')?>"></a></li>
          <li><a class="fa fa-instagram" target="_blank" href="<?=$this->text('social-account-instagram') ?>" title="<?= $this->t('regular-share-instagram')?>"></a></li>
          <li><a class="fa fa-telegram" target="_blank" href="<?=$this->text('social-account-telegram') ?>" title="<?= $this->t('regular-share-telegram')?>"></a></li>
          <li><a class="fa fa-github" target="_blank" href="<?=$this->text('social-account-github') ?>" title="<?= $this->t('regular-share-github')?>"></a></li>
          <li><a class="fa fa-newspaper-o" target="_blank" href="<?= $this->lang_host() ?>newsletter" title="<?= $this->t('regular-share-newsletter)')?>"></a></li>
        </ul>
      </div>
    </div>
</footer>



