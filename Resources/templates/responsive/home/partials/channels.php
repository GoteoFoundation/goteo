<?php if($this->channels && count($this->channels) > 1): ?>

    <div class="section channels" id="channels" >
        <div class="drop-img-container">
            <img class="center-block drop-img" src="/assets/img/project/channel.svg" alt="matchfunding">
        </div>
        <h2 class="title text-center">
            <?= $this->text('home-channels-title') ?>
        </h2>
        <div class="container" id="channel-container">
            <div class="row slider slider-channels">
            <?php foreach($this->channels as $channel): ?>
                <?php $summary = $channel->getSummary(); ?>
                <?php $background = $channel->owner_background; ?>
                <?= $channel->owner_font_color ?>

                <div class="channel col-sm-4">
                        <div class="widget-channel">
                            <div class="img-container" style="background-color: <?= $background ?> ">
                                <div class="img">
                                    <a class="a-unstyled" href="<?= '/channel/'.$channel->id ?>">
                                        <img class="img-responsive" src="<?= $channel->logo ? $channel->logo->getlink(200,0) : '' ?>" alt="<?= $channel->name ?>"/>
                                    </a>
                                </div>
                            </div>
                            <div class="content" style="<?php if($background) echo ' background-color:' . $this->to_rgba($background, 0.8); if($channel->owner_font_color) echo ' color:' . $channel->owner_font_color; ?>" >
                                <div class="title">
                                    <a class="a-unstyled" href="<?= '/channel/'.$channel->id ?>">
                                    <?= $channel->name ?>
                                    </a>
                                </div>
                                <div class="description">
                                    <?= $this->text_truncate($channel->description, 120) ?>
                                </div>
                            </div>
                        </div>
                </div>
            <?php endforeach ;?>
            </div>
        </div>

    </div>

<?php endif; ?>
