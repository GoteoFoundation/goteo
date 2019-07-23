<?php 

$date=new Datetime($this->workshop->date_in);
$date_now=new DateTime("now"); 

$interval=$date->diff($date_now);

$days=$interval->days;

?>

<div class="fluid-container call-to-action-component extra-call-to-action">
    <div class="container">
        <div class="row call-action">
            <div class="col-sm-9 col-time">
                <span><i class="fa fa-clock-o"></i><?= $this->text('workshop-count-down', $days) ?></span>
            </div>
            <div class="col-sm-3 col-button">
                <a target="_blank" href="<?= $this->workshop->url ?>" class="btn btn-white"><?= $this->text('workshop-register') ?></a>
            </div>
        </div>
    </div>
</div>