<?php
$categories = $this->project->getCategories();
$social = $this->ee($this->project->getSocialCommitment());
$target = 'target="_blank"';
$link = $this->link;
if (!$link) {
    $link = '/project/' . $this->project->id;
}
if ($this->admin && !$this->link) {
    $link = '/dashboard' . $link;
    $target = '';
}

$widget = $this->project;
$typeOfCampaign = $this->project->getConfig()->getType();

if (!($label = $this->label)) {
    $call = $widget->getCall();
    $matchers = $this->project->getMatchers('active', ['has_channel' => true]);
}

?>

<article class="project-widget flip-widget normal<?= $widget->isApproved() ? '' : ' non-public' ?> permanent" id="project-<?= $widget->id ?>">
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
    <?php endif; ?>

    <section>
        <a class="img-link" href="<?= $link ?>" <?= $target ?>>
            <img loading="lazy" class="img-project" src="<?= $widget->image->getLink(600, 416, true); ?>" alt="<?= $this->text('regular-header-image-of', $widget->name) ?>">
            <h2><?= $this->text_truncate($this->ee($widget->name), 80); ?></h2>
        </a>
    </section>


    <a class="floating flip" href="#backflip-<?= $widget->id ?>" title="<?= $this->text('regular-more_info') ?>">+</a>

    <section class="content">
        <h3>
            <a href="/user/profile/<?= $widget->user->id ?>" <?= $target ?>><?= $this->text('regular-by') . ' ' . $widget->user->name ?></a>
        </h3>
        <p class="description">
            <?= $this->text_truncate($this->ee($widget->subtitle), 140) ?>
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

    </section>
    <section class="bottom">
        <div class="amount">
            <ul>
                <li><strong><?= amount_format($widget->amount) ?></strong> <?= $this->text('horizontal-project-reached') ?></li>
                <li><strong><?= $widget->num_investors ?></strong> <?= $this->text('project-view-metter-investors') ?></li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </section>

    <?php if ($this->admin) : ?>
        <?= $this->insert('project/widgets/partials/backside_admin') ?>
    <?php elseif ($call) : ?>
        <?= $this->insert('project/widgets/partials/backside_call', ['call' => $call]) ?>
    <?php else : ?>
        <?= $this->insert('project/widgets/partials/backside_normal', ['widget' => $widget]) ?>
    <?php endif ?>
</article>
