<?php

$link = $this->link ? $this->link : '/matcher/' . $this->matcher->id;
$sphere = $this->matcher->getMainSphere();
?>

<div class="flip-widget call-widget normal open" id="call-<?= $this->matcher->id ?>">
    <div class="status">
        <?= $this->text('call-tagmark-open') ?>
    </div>

    <div class="sphere">
        <?php if($sphere): ?>
            <img class="center-block" src="<?= $sphere->getImage()->getLink(60, 60, false) ?>">
            <div class="text-center name"><?= $sphere->name ?></div>
        <?php endif; ?>
    </div>

    <a class="floating flip" href="#backflip-<?= $this->matcher->id ?>" title="<?= $this->text('regular-more_info') ?>">+</a>

    <div class="content">
        <div class="amount-label">
            <?= $this->text('matcher-budget-amount') ?>
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

