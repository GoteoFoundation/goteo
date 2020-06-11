<?php
  $stories = $this->channel->getStories();

  if ($stories):
?>

<div class="section stories">
  <div class="spacer-20 slider slider-stories">
    <?php foreach ($stories as $key => $story): ?>
      <div class="story-container">
        <img src='<?= $story->getBackgroundImage()->getLink(1350,400,true) ?>'>
        <div class="story">
          <div class="row">
            <div class="col-md-7 col-sm-9">
              <h2 class="title">
                <span class="icon icon-channel-chat icon-3x"></span>
                <span><?= $this->t('channel-call-stories-title') ?></span>
              </h2>
            </div>
            <div class="col-md-4 col-sm-2">
              <div class="green-line"></div>
            </div>
          </div>
      
          <div class="info col-md-10 col-sm-11">
            <q><?= $story->description ?></q>
            <div class="author" >
              <?php if ($story->pool_image): ?>
                <img class="author-avatar" src="<?= $story->getImage()->getLink(70,70, true) ?>">
              <?php endif; ?>
              <?php $title=explode("/", $story->title); ?>
              <div class="author-name">
                <?= $title[0] ?> <br>
                <?= $title[1] ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>