<?php
  $stories = $this->channel->getStories();

  if ($stories):
?>

<div class="section stories">
  <div class="container">
    <div class="row">
      <div class="col-md-4 col-sm-6">
        <h2 class="title">
            <span class="icon icon-channel-chat icon-3x"></span>
            <span><?= $this->t('channel-call-stories-title') ?></span>
        </h2>
      </div>
      <div class="col-md-8 col-sm-6">
        <div class="white-line"></div>
      </div>
    </div>

    <div class="spacer-20 slider slider-stories">
    <?php foreach ($stories as $key => $story): ?>
      <div class="story">
        <div class="col-md-4 col-sm-4" align="center">
          <div class="image">
            <img src="<?= $story->getImage()->getLink(150,150) ?>" >
          </div>
        </div>
        <div class="col-md-8 col-sm-8">
          <p><?= $story->description ?></p>
          <div class="author" >
            <?= "- ".$story->title ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>