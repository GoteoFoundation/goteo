<div class="section sponsors" >
    <?php if($this->sponsors): ?>
        <div class="container">
            <?php foreach($this->sponsors as $type => $sponsors): ?> 
                <?php if ($sponsors): ?>
                <div class="row">
                    <div class="sponsor-type">
                        <h2 class="title text-center"><?= $this->t('home-sponsor-type-' . $type )?></h2>
                    </div>
                </div>
                <div class="row">
                    <?php foreach($sponsors as $sponsor): ?>
                        <div class="col-md-4 col-sm-6 col-xs-12 sponsor-item">
                            <a href="<?= $sponsor->url ?>">
                                <img alt="<?= $sponsor->name ?>" src="<?= $sponsor->image->getLink(250, 70, false) ?>" />
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>