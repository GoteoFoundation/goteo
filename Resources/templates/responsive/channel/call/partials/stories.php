<?php
  $stories = $this->channel->getStories();

  if ($stories):
?>

<div class="section stories">
  <div class="spacer-20 slider slider-stories">
    <?php foreach ($stories as $key => $story): ?>
    <?php $story->image = 'story-background.png'; ?>
      <div class="container">
        <img src='<?= $story->getImage()->getLink(1350,400,true) ?>'>
        <div class="story">
          <div class="row">
            <div class="col-md-7 col-sm-8">
              <h2 class="title">
                <span class="icon icon-channel-chat icon-3x"></span>
                <span><?= $this->t('channel-call-stories-title') ?></span>
              </h2>
            </div>
            <div class="col-md-4 col-sm-3">
              <div class="green-line"></div>
            </div>
          </div>
      
          <div class="info col-md-10 col-sm-8">
            <q><?= $story->description ?></q>
            <div class="author" >
              <img class="author-avatar" src="<?= $story->getImage()->getLink(50,50) ?>">
              <?= "- ".$story->title ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>