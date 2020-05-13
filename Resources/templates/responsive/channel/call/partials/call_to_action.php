<div class="section call-to-action">
  <div class="container">
      <div class="row">

        <?php if ($this->channel->call_inscription_open): ?>
          <div class="col-md-6 col-sm-12">
            <div class="create-project">
              <img class="img-responsive" src="/assets/img/channel/call/create_project.png" >
              <div class="info">
                <div class="title">
                  <?= $this->t('channel-call-cta-create-project-title') ?>
                </div>
                <div class="description">
                  <?= $this->t('channel-call-cta-create-project-description') ?>
                </div>
                <div class="col-button">
                    <a href="/project/create" class="btn btn-transparent"><i class="icon icon-plus icon-2x"></i><?= $this->text('landing-more-info') ?></a>
                </div>
              </div>
            </div>
          </div>
  
          <div class="col-md-6 col-sm-12 join-call">
            <div class="join-call">
              <img class="img-responsive" src="/assets/img/channel/call/join_program.png" >
              <div class="info">
                <div class="title">
                  <?= $this->t('channel-call-cta-join-program-title') ?>
                </div>
                <div class="description">
                  <?= $this->t('channel-call-cta-join-program-description') ?>
                </div>
                <div class="col-button">
                    <a href="/project/create" class="btn btn-yellow"><i class="icon icon-plus icon-2x"></i><?= $this->text('landing-more-info') ?></a>
                </div>
              </div>
            </div>
          </div>
        <?php else : ?>
            <div class="row create-project">
              <div class="col-md-6">
                <img class="img-responsive" src="/assets/img/channel/call/create_project.png" >
              </div>
              <div class="col-md-6">
                  <div class="info">
                    <div class="title">
                      <?= $this->t('channel-call-cta-create-project-title') ?>
                    </div>
                    <div class="description">
                      <?= $this->t('channel-call-cta-create-project-description') ?>
                    </div>
                    <div class="col-button">
                        <a href="<?= '/channel/'.$this->channel->id.'/create' ?>" class="btn btn-transparent"><i class="icon icon-plus icon-2x"></i><?= $this->text('landing-more-info') ?></a>
                    </div>
                  </div>
                <div>
          </div>
        <?php endif; ?>
      </div>

  </div>

</div>