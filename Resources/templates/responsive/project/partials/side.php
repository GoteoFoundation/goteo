<?php $project=$this->project; ?>

<div class="hidden-xs">

        <h2 class="green-title">
        <?= $this->text('project-rewards-side-title') ?>
        </h2>

        <?php foreach ($this->individual_rewards as $individual) : ?>

        <?php $available = $individual->available(); ?>
        <?php $units = ($individual->units - $individual->taken); // units left?>
        <div class="side-widget">

            <div class="amount"><?= $this->text('regular-investing').' '.amount_format($individual->amount); ?></div>
            <div class="text-bold spacer-20"><?= $individual->reward ?></div>
            <div class="spacer-20"><?= $this->text_url_link($individual->description) ?></div>

            <div class="investors">
                <div><?= '> '.sprintf("%02d", $individual->taken).' '.$this->text('project-view-metter-investors') ?></div>
                <?php if ($project->inCampaign()): ?>

                <?php if (!$available):  ?>
                    <div class="left"><?= ' > '.$this->text('invest-reward-none') ?></div>
                <?php elseif (!empty($individual->units)) : ?>
                    <div class="left">
                        <?= ' > '.$this->text('project-rewards-individual_reward-units_left', sprintf("%02d", $units)) ?>
                    </div>
                <?php endif ?>

                <?php endif; ?>
            </div>


            <?php if ($project->inCampaign()): ?>

                <div class="spacer-5">
                    <?php if(!$individual->none): ?>
                    <a href="<?= '/invest/'.$project->id.'/payment?reward='.$individual->id ?>"><button class="btn btn-block btn-pink"><?= $this->text('regular-getit') ?></button></a>
                        <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>
        <?php endforeach ?>

        <h2 class="green-title spacer">
        <?= $this->text('project-menu-messages') ?>
        </h2>
        <div class="small-subtitle">
        &gt; <?= $project->num_messengers.' '.$this->text('project-collaborations-number') ?>
        </div>
        <div class="small-subtitle">
        &gt; <?= count($project->supports).' '.$this->text('project-collaborations-available') ?>
        </div>

        <div class="collaborations">
        <!-- List of collaborations -->
        <?php foreach ($project->supports as $support): ?>
            <div class="side-widget">
                <div class="text-bold"><?= $support->support ?></div>
                <div class="spacer-20"><?= substr($support->description,0,150) ?></div>
                <div class="spacer-5">
                    <a href="<?= '/project/'.$project->id.'/participate#msg-'.$support->thread ?>"><button class="btn btn-block btn-green"><?= $this->text('regular-collaborate') ?></button></a>
                </div>
            </div>
        <?php endforeach ?>
        </div>
</div>
