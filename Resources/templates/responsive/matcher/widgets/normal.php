<?php

$link = $this->link ? $this->link : '/matcher/' . $this->matcher->id;

?>

<div class="flip-widget call-widget normal open" id="call-<?= $this->matcher->id ?>">
    <div class="status">
        <?= $this->text('call-tagmark-open') ?>
    </div>

    <div class="sphere">
        <div class="text-center name"><?= $this->matcher->name ?></div>
    </div>

    <a class="floating flip" href="#backflip-<?= $this->matcher->id ?>" title="<?= $this->text('regular-more_info') ?>">+</a>

    <div class="content">
        <div class="amount-label">
            Presupuesto de riego
        </div>
        <div class="amount">
           <?= amount_format($this->matcher->amount) ?>
        </div>
        <div class="bottom" >
            <div class="pull-left">
                <img src="<?= $this->matcher->getOwner()->avatar->getLink(70) ?>">
            </div>
        </div>
    </div>

<?= $this->insert('matcher/widgets/partials/backside_normal') ?>

</div>

