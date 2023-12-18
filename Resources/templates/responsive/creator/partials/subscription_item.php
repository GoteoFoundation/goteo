<?php
$project = $this->project;
$subscription = $this->subscription;
?>

<article class="subscription">
    <div class="card-header">
        <h2 title="<?= $subscription->reward ?>"><?= $subscription->reward ?></h2>
    </div>
    <div class="card-body">
        <div class="amount-box text-center text-uppercase">
            <span class="amount"><?= amount_format($subscription->amount) ?></span>
        </div>

        <p>
            <?= $this->markdown($subscription->description) ?>
        </p>
    </div>
    <div class="card-footer">
        <div class="reward-donate text-center">
            <a class="btn btn-lg btn-cyan text-uppercase" href="/invest/<?= $project->id ?>/<?= $subscription->id ?>">
                <?php if ($subscription->subscribable): ?>
                    <?= $this->text('project-recurring-support') ?>
                <?php else: ?>
                    <?= $this->text('project-regular-support') ?>
                <?php endif; ?>
            </a>
        </div>
    </div>
</article>
