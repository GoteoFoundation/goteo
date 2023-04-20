<?php
    $project = $this->project;
    $reward = $this->reward
?>

<article class="card reward-info">
    <div class="card-header">
        <h2 title="<?= $reward->reward ?>"><?= $reward->reward ?></h2>
    </div>
    <div class="card-body">
        <div class="amount-box text-center text-uppercase">
            <span class="amount"><?= amount_format($reward->amount) ?></span>
        </div>

        <p>
            <?= $this->markdown($reward->description) ?>
        </p>
    </div>
    <div class="card-footer">
        <div class="reward-donate text-center">
            <a class="btn btn-lg btn-cyan text-uppercase" href="/invest/<?= $project->id ?>/<?= $reward->id ?>">
                <?= $this->text('project-regular-support') ?>
            </a>
        </div>
    </div>
</article>
