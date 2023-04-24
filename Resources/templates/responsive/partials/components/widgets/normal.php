<?php

$project = $this->widget_project($this->project);

$percent = $project->getAmountPercent();
$categories = $project->getCategories();
// $social_rewards = $project->getSocialRewards();
$social = $this->ee($project->getSocialCommitment());
$target = 'target="_blank"';
$link = $this->link;
if (!$link) {
    $link = '/project/' . $project->id;
}
if ($this->admin && !$this->link) {
    $link = '/dashboard' . $link;
    $target = '';
}
if (!($label = $this->label)) {
    $tagmark = $project->getTagmark();
    $call = $project->getCall();
    $matchers = $project->getMatchers('active', ['has_channel' => true]);
}

?>

<div class="project-widget flip-widget normal<?= $project->isApproved() ? '' : ' non-public' ?>" id="project-<?= $project->id ?>">
    <?php if ($label) : ?>
        <div class="status btn-lilac">
            <?= $label ?>
        </div>
    <?php elseif ($call) : ?>
        <div class="status btn-lilac">
            <i class="icon icon-call"></i> <?= $this->text('regular-call') ?> x<strong>2</strong>
        </div>
    <?php elseif ($matchers) : ?>
        <div class="status btn-lilac">
            <i class="icon icon-call"></i> <?= $matchers[0]->name ?>
        </div>
    <?php elseif ($tagmark) : ?>
        <div class="status btn-orange">
            <?php
            if ($tagmark === 'onrun-keepiton')
                echo $this->text('regular-onrun_mark') . ' ' . $this->text('regular-keepiton_mark');
            else
                echo $this->text('regular-' . $tagmark . '_mark')
                ?>
        </div>
    <?php endif; ?>


    <a class="img-link" href="<?= $link ?>" <?= $target ?>>
        <img loading="lazy" class="img-project" src="<?= $project->image->getLink(600, 416, true); ?>" alt="<?= $this->text('regular-header-image-of', $project->name) ?>">
        <h2><?= $this->text_truncate($this->ee($project->name), 80); ?></h2>
    </a>

    <a class="floating flip" href="#backflip-<?= $project->id ?>" title="<?= $this->text('regular-more_info') ?>">+</a>

    <div class="content">
        <h3>
            <a href="/user/profile/<?= $project->user->id ?>" <?= $target ?>><?= $this->text('regular-by') . ' ' . $project->user->name ?></a>
        </h3>
        <p class="description">
            <?= $this->text_truncate($this->ee($project->subtitle), 140) ?>
        </p>

        <?php // TODO: add links here?
        if ($categories) : ?>
            <ul class="categories list-inline comma-list">
                <i class="fa fa-tag"></i>
                <?php foreach($categories as $category): ?>
                    <li><?= $category ?></li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

        <div class="bottom">
            <div class="amount">
                <ul>
                    <li><strong><?= amount_format($project->amount) ?></strong> <?= $this->text('horizontal-project-reached') ?></li>
                    <li><strong><?= $project->getDaysLeft() ?></strong> <?= $project->getStatusDescription() ?></li>
                </ul>
                <div class="clearfix"></div>
            </div>

            <div class="percent">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $percent ?>%">
                    </div>
                </div>
                <p><?= $percent . '% ' . ucfirst($this->text('horizontal-project-percent')) ?></p>
            </div>
        </div>
    </div>

    <?php if ($this->admin) : ?>
        <?= $this->insert('partials/components/widgets/partials/backside_admin') ?>
    <?php elseif ($call) : ?>
        <?= $this->insert('partials/components/widgets/partials/backside_call', ['call' => $call]) ?>
    <?php else : ?>
        <?= $this->insert('partials/components/widgets/partials/backside_normal') ?>
    <?php endif ?>
</div>
