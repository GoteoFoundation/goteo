<?php $date=new Datetime($this->workshop->date_in); ?>
<?php $month=strtolower(strftime("%B",$date->getTimestamp())); ?>
<div class="fluid-container call-to-action-component main-call-to-action">
    <div class="container">
        <div class="row call-action">
            <div class="col-sm-3">
                <div class="date">
                    <div class="left">
                        <?= $date->format('d'); ?>
                    </div>
                    <div class="right">
                        <div class="month">
                            <?= $this->text('date-'.$month); ?>
                        </div>
                        <div class="year">
                            <?= $date->format('Y'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-location">
                <span><i class="fa fa-map-marker"></i><?= $this->workshop->venue ?></span>
            </div>
            <?php if(!$this->workshop->expired()): ?>
            <div class="col-sm-3 col-button">
                <a target="_blank" href="<?= $this->workshop->url ?>" class="btn btn-white"><?= $this->text('workshop-register') ?></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>