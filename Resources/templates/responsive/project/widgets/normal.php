<?php

$percent = $this->project->getAmountPercent();
$categories = $this->project->getCategories();
// $social_rewards = $this->project->getSocialRewards();
$social = $this->project->getSocialCommitment();
$link = $this->link ? $this->link : '/project/' . $this->project->id;
$tagmark = $this->project->getTagmark();

?><div class="project-widget normal" id="project-<?= $this->project->id ?>">

    <?php if($tagmark): ?>
        <div class="status btn-orange">
        <?php
           if($tagmark === 'onrun-keepiton')
                echo $this->text('regular-onrun_mark') . ' ' . $this->text('regular-keepiton_mark');
            else
                echo $this->text('regular-' . $tagmark . '_mark')
        ?>
        </div>
    <?php endif; ?>


    <a class="img-link" href="<?= $link ?>">
        <img class="img-project" src="<?= $this->project->image->getLink(300, 208, true); ?>">
        <h2><?= $this->text_truncate($this->project->name, 80); ?></h2>
    </a>

    <a class="flip" href="#backflip-<?= $this->project->id ?>" title="<?= $this->text('regular-more_info') ?>">+</a>

    <div class="content">
        <h4>
            <a href="/user/profile/<?= $this->project->user->id?>" target="_blank"><?= $this->text('regular-by').' '.$this->project->user->name ?></a>
        </h4>
        <div class="description">
            <?= $this->text_truncate($this->project->description, 140) ?>
        </div>

        <?php // TODO: add links here?
        if($categories): ?>
        <div class="categories">
            <i class="fa fa-tag"></i> <?= implode(", ", $categories) ?>
        </div>
        <?php endif ?>

        <div class="amount">
            <ul>
                <li><strong><?= amount_format($this->project->amount) ?></strong> <?= $this->text('horizontal-project-reached') ?></li>
                <li><strong><?= $this->project->getDaysLeft() ?></strong> <?= $this->project->getStatusDescription() ?></li>
            </ul>
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

    <div class="backside" id="backflip-<?= $this->project->id ?>">
        <div class="flip" href="#backflip-<?= $this->project->id ?>">✖</div>

        <ul class="data-list">
            <li>
                <h5><?= $this->text('project-obtained') ?></h5>
                <p><strong><?= amount_format($this->project->amount) ?></strong></p>
            </li>
            <li>
                <h5><?= $this->text('project-view-metter-minimum') ?></h5>
                <p><?= amount_format($this->project->mincost) ?></p>
            </li>
            <li>
                <h5><?= $this->text('project-view-metter-optimum') ?></h5>
                <p><?= amount_format($this->project->maxcost) ?></p>
            </li>
            <li class="divider"></li>
            <li>
                <h5><?= $this->project->num_investors . ' ' . $this->text('project-menu-supporters') ?></h5>
            </li>
            <?php
                if($this->project->project_location):
                // TODO: link this to some map?
             ?>
            <li class="divider"></li>
            <li>
                <h6><a><i class="fa fa-map-marker"></i> <?= $this->project->project_location ?></a></h6>
            </li>
            <?php endif ?>
            <?php if($social): ?>
            <li class="divider"></li>
            <li>
                <h5><?= $this->text('project-social-commitment-title') ?></h5>
                <p class="social">
                    <img src="<?= $social->image->getLink(60, 60, false) ?>">
                    <?= $social->name ?>
                </p>
            </li>
            <?php endif ?>
        </ul>

        <div class="invest">
            <a class="btn btn-lg btn-block btn-dark-pink" href="/invest/<?= $this->project->id ?>"><?= $this->text('project-regular-support') ?></a>
        </div>
    </div>
</div>
