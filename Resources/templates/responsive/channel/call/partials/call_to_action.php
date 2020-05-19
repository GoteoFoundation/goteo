<?php

  $call_to_actions = $this->channel->getCallToActions();

  if ($call_to_actions): 
?>

<div class="section call-to-action">
  <div class="container">
      <div class="row">
        <?php foreach ($call_to_actions as $cta): ?>
          <div class="col-md-6 col-sm-12">
            <div class="cta <?= $cta->style?>" >
              <img class="img-responsive" src="<?= $cta->header->getLink(500,300)?>" >
              <div class="info">
                <div class="title">
                  <?= $cta->title ?>
                </div>
                <div class="description">
                  <?= $cta->description ?>
                </div>
                <div class="col-button">
                    <a href="<?= $cta->action_url ?>" class="btn btn-transparent"><i class="icon icon-<?= $cta->icon ?> icon-2x"></i><?= $cta->action ?></a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
  </div>
</div>

<?php endif; ?>