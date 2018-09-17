<div class="slider slider-stories" id="stories">
<?php foreach($this->stories as $story): ?>
    <div class="row">
        <div class="col-md-6">
            <img class="img-responsive" src="<?= $story->getImage()->getLink(600, 400, true) ?>" >
        </div>
        <div class="col-md-6">
            <div class="info-container">
                <div class="type-container">
                    <span class="type-label" >
                        <?= $this->text('home-foundation-story-label') ?>
                    </span>
                    <?php if($story->review): ?>
                        <span class="type hidden-xs">
                            <?= $story->review ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="description">
                    <div class="pull-left quote">
                        <i class="fa fa-quote-left"></i>
                    </div>
                    <div class="pull-left text">
                        <?= $story->description ?>
                        <i class="fa fa-quote-right pull-right"></i>
                    </div>
                </div>
                <div class="author" >
                    <?= "- ".$story->title ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

</div>