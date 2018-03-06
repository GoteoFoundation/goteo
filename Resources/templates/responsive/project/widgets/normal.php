<?php

$percent = $this->project->getAmountPercent();
$categories = $this->project->getCategories();
// $social_rewards = $this->project->getSocialRewards();
$social = $this->project->getSocialCommitment();
$target = 'target="_blank"';
$link = $this->link;
if(!$link) {
    $link = '/project/' . $this->project->id;
}
if($this->admin && !$this->link) {
    $link = '/dashboard' . $link;
    $target = '';
}
if(!($label = $this->label)) {
    $tagmark = $this->project->getTagmark();
    $call = $this->project->getCall();
    $matchers = $this->project->getMatchers('active');
}

?><div class="project-widget flip-widget normal<?= $this->project->isApproved() ? '' : ' non-public' ?>" id="project-<?= $this->project->id ?>">

    <?php if($label): ?>
        <div class="status btn-lilac">
            <?= $label ?>
        </div>
    <?php elseif($call): ?>
        <div class="status btn-lilac">
            <i class="icon icon-call"></i> <?= $this->text('regular-call') ?> x<strong>2</strong>
        </div>
    <?php elseif($matchers): ?>
        <div class="status btn-lilac">
            <i class="icon icon-call"></i> <?= $matchers[0]->name ?>
        </div>
    <?php elseif($tagmark): ?>
        <div class="status btn-orange">
        <?php
           if($tagmark === 'onrun-keepiton')
                echo $this->text('regular-onrun_mark') . ' ' . $this->text('regular-keepiton_mark');
            else
                echo $this->text('regular-' . $tagmark . '_mark')
        ?>
        </div>
    <?php endif; ?>


    <a class="img-link" href="<?= $link ?>" <?= $target ?>>
        <img class="img-project" src="<?= $this->project->image->getLink(600, 416, true); ?>">
        <h2><?= $this->text_truncate($this->project->name, 80); ?></h2>
    </a>

    <a class="floating flip" href="#backflip-<?= $this->project->id ?>" title="<?= $this->text('regular-more_info') ?>">+</a>

    <div class="content">
        <h4>
            <a href="/user/profile/<?= $this->project->user->id?>" <?= $target ?>><?= $this->text('regular-by').' '.$this->project->user->name ?></a>
        </h4>
        <div class="description">
            <?= $this->text_truncate($this->project->subtitle, 140) ?>
        </div>

        <?php // TODO: add links here?
        if($categories): ?>
        <div class="categories">
            <i class="fa fa-tag"></i> <?= implode(', ', $categories) ?>
        </div>
        <?php endif ?>

        <div class="bottom">
            <div class="amount">
                <ul>
                    <li><strong><?= amount_format($this->project->amount) ?></strong> <?= $this->text('horizontal-project-reached') ?></li>
                    <li><strong><?= $this->project->getDaysLeft() ?></strong> <?= $this->project->getStatusDescription() ?></li>
                </ul>
                <div class="clearfix"></div>
            </div>

            <div class="percent">
                <div class="progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="<?= $percent ?>"
                  aria-valuemin="0" aria-valuemax="100" style="width:<?= $percent ?>%">
                  </div>
                </div>
                <p><?= $percent . '% ' . ucfirst($this->text('horizontal-project-percent')) ?></p>
            </div>
        </div>
    </div>

  <?php if($this->admin): ?>
    <?= $this->insert('project/widgets/partials/backside_admin') ?>
  <?php elseif($call): ?>
    <?= $this->insert('project/widgets/partials/backside_call', ['call' => $call]) ?>
  <?php else: ?>
    <?= $this->insert('project/widgets/partials/backside_normal') ?>
  <?php endif ?>
</div>
