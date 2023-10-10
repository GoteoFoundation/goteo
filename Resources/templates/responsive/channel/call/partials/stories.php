<?php
  $stories = $this->channel->getStories();
  $section = current($this->channel->getSections('stories'));

  if ($section && $stories):
?>

<section class="section stories">
  <div class="slider slider-stories">

    <?php foreach ($stories as $key => $story): ?>
      <?php $title=explode("/", $story->title); ?>
      <div class="story-container">
        <div class="story-desktop-container">
          <?php
            $background_image = $story->getBackgroundImage();
            $credits = $background_image->getCredits();
          ?>
          <picture>
            <source media="(min-width:1400px)" srcset="<?= $background_image->getLink(1920,400,true) ?>">
            <source media="(min-width:1051px)" srcset="<?= $background_image->getLink(1400,400,true) ?>">
            <source media="(min-width:750px)" srcset="<?= $background_image->getLink(1051,400,true) ?>">
            <img src='<?= $background_image->getLink(750,400,true) ?>' alt="<?= $story->title ?>">
          </picture>
          <?php
            if ($credits):
          ?>
            <figcaption>
              <cite>
                <?= $credits->credits ?>
              </cite>
            </figcaption>
          <?php endif; ?>
          <div class="story">
            <div class="row">
              <div class="col-md-7 col-sm-9">
                <h2 class="title">
                  <span class="icon icon-channel-chat icon-3x"></span>
                  <span><?= $section->main_title ?: $this->t('channel-call-stories-title') ?></span>
                </h2>
              </div>
              <div class="col-md-4 col-sm-2">
                <div class="green-line"></div>
              </div>
            </div>

            <div class="info col-md-10 col-sm-11">
              <q><?= $story->description ?></q>
              <div class="author" >
                <?php if ($story->image): ?>
                  <img loading="lazy" class="author-avatar" src="<?= $story->getImage()->getLink(70,70, true) ?>">
                <?php endif; ?>
                <div class="author-name">
                  <?= $title[0] ?> <br>
                  <?= $title[1] ?>
                </div>
                <?php if ($story->url): ?>
                    <a href="<?= $story->url ?>" class="btn">
                        <i class="icon icon-plus icon-2x"></i> <?= $this->t('channel-call-stories-cta') ?>
                    </a>
                <?php endif; ?>
              </div>
          </div>
        </div>

        <div class="story-xs">
          <div class="row">
            <div class="col-md-6">
                <img loading="lazy" class="img-responsive" src="<?= $story->getBackgroundImage()->getLink(600, 400, true) ?>" >
            </div>
            <div class="col-md-6">
                <div class="info-container">
                    <q><?= $story->description ?></q>

                    <div class="author" >
                      <?= $title[0] ?> <br>
                      <?= $title[1] ?>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
