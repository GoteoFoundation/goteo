<?php $project=$this->project; ?>        

<div class="hidden-xs">

        <h2 class="green-title">
        <?= $this->text('project-rewards-side-title') ?>
        </h2>

        <?php foreach ($project->individual_rewards as $individual) : ?>
        <div class="side-widget">

            <div class="amount"><?= $this->text('regular-investing').' '.amount_format($individual->amount); ?></div>
            <div class="text-bold spacer-20"><?= $individual->reward ?></div>
            <div class="spacer-20"><?= $individual->description ?></div>

            <div class="investors">
                <?= '> '.sprintf("%02d", $individual->taken).' '.$this->text('project-view-metter-investors') ?>
            </div>
            <?php if ($project->status==3): ?>

                <div class="row spacer-5">
                    <div class="col-md-6 col-sm-8">
                        <?php if($individual->none): ?>
                        <a href="<?= '/invest/'.$project->id.'/payment?amount='.$individual->amount ?>"><button class="btn btn-block side-pink"><?= $this->text('landing-donor-button') ?></button></a>
                        <?php else: ?>
                        <a href="<?= '/invest/'.$project->id.'/payment?reward='.$individual->id ?>"><button class="btn btn-block side-pink"><?= $this->text('regular-getit') ?></button></a>
                        <?php endif; ?>
                    </div>  
                </div>

            <?php endif; ?>

        </div>
        <?php endforeach ?>

        <h2 class="green-title spacer">
        <?= $this->text('project-menu-messages') ?>
        </h2>
        <div class="small-subtitle">
        <?= '> '.$project->num_messengers.' '.$this->text('project-collaborations-number') ?>
        </div>
        <div class="small-subtitle">
        <?=  '> '.sprintf("%02d", count($project->supports)).' '.$this->text('project-collaborations-available') ?>
        </div>

        <div class="collaborations">
        <!-- List of collaborations -->
        <?php foreach ($project->supports as $support): ?>
            <div class="side-widget">
                <div class="text-bold"><?= $support->support ?></div>
                <div class="spacer-20"><?= substr($support->description,0,150) ?></div>
                <div class="row spacer-5">
                    <div class="col-md-6 col-sm-8">
                        <a href="<?= '/project/'.$project->id.'/participate#msg-'.$support->thread ?>"><button class="btn btn-block green"><?= $this->text('regular-collaborate') ?></button></a>
                    </div>  
                </div>
            </div>
        <?php endforeach ?>
        </div>
</div>