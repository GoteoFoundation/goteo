<?php $project = $this->project; ?>

<div class="hidden-xs">
    <h2 class="green-title">
        <?= $this->text('project-rewards-side-title') ?>
    </h2>

    <ul class="list-unstyled">
        <?php foreach ($this->individual_rewards as $individual) :
            $available = $individual->available();
            $units = ($individual->units - $individual->taken); // units left
            $matchedReward = $individual->isMatched();
        ?>
            <li
                id="reward-<?= $individual->id ?>"
                class="side-widget
                    <?= $matchedReward ? "matched-reward" : '' ?>
                    <?= $individual->subscribable ? 'reward-subscribable' : '' ?>"    
            >
                <article>
                    <?php if ($matchedReward) : ?>
                        <div class="btn-lilac">
                            <i class="icon icon-call"></i> <?= $this->t('regular-call') ?>
                            <i class="fa fa-info-circle" data-html="true" data-container="body" data-toggle="tooltip" title="" data-original-title="<span style='font-size: 16px;'><?= $this->text('matcher-matches-this-reward') ?></span>">
                            </i>
                        </div>
                    <?php endif; ?>
                    <div class="reward-info">
                        <h3 class="amount"><?= $this->text('regular-investing') . ' ' . amount_format($individual->amount); ?></h3>
                        <p class="text-bold spacer-20"><?= $individual->reward ?></p>
                        <p class="spacer-20"><?= $this->markdown($individual->description) ?></p>

                        <div class="investors">
                            <p><?= '> ' . sprintf("%02d", $individual->taken) . ' ' . $this->text('project-view-metter-investors') ?></p>

                            <?php if ($project->inCampaign()) : ?>
                                <?php if (!$available) :  ?>
                                    <p class="left"><?= ' > ' . $this->text('invest-reward-none') ?></p>
                                <?php elseif (!empty($individual->units)) : ?>
                                    <p class="left">
                                        <?= ' > ' . $this->text('project-rewards-individual_reward-units_left', sprintf("%02d", $units)) ?>
                                    </p>
                                <?php endif ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($project->inCampaign() && !$individual->none) : ?>
                            <a class="btn btn-block btn-pink spacer-5" href="<?= '/invest/' . $project->id . '/payment?reward=' . $individual->id ?>">
                                <span><?= $this->text('regular-getit') ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            </li>
        <?php endforeach ?>
    </ul>

    <h2 class="green-title spacer">
        <?= $this->text('project-menu-messages') ?>
    </h2>

    <ul class="collaboration-list">
        <li class="small-subtitle">
            <?= $project->num_messengers . ' ' . $this->text('project-collaborations-number') ?>
        </li>
        <li class="small-subtitle">
            <?= count($project->supports) . ' ' . $this->text('project-collaborations-available') ?>
        </li>
    </ul>

    <ul class="collaborations list-unstyled">
        <?php foreach ($project->supports as $support) : ?>
            <li class="side-widget">
                <article id="support-<?= $support->id ?>" class="support-widget">
                    <h4 class="text-bold">
                        <?= $support->support ?>
                    </h4>
                    <p class="spacer-20" title="<?= $support->description ?>">
                        <?= substr($support->description, 0, 150) ?>
                    </p>
                    <div class="spacer-5">
                        <a class="btn btn-block btn-green" href="<?= '/project/' . $project->id . '/participate#msg-' . $support->thread ?>">
                            <?= $this->text('regular-collaborate') ?>
                        </a>
                    </div>
                </article>
            </li>
        <?php endforeach ?>
    </ul>
</div>