<div class="backside" id="backflip-<?= $this->matcher->id ?>">
    <a class="close flip" href="#backflip-<?= $this->matcher->id ?>"><i class="icon icon-close"></i></a>
    <div class="status">
        <?= $this->text_truncate($this->matcher->name, 60) ?>
    </div>

    <div class="sphere">
        <?= $this->text_truncate(strip_tags($this->markdown($this->matcher->terms)), 150) ?>
    </div>

    <div class="content">
        <div class="date">
            <div>
                <strong><?= $this->text('call-remain-drop') ?></strong>
            </div>
            <div class="date-data">
                <?= amount_format($this->matcher->getAvailableAmount()) ?>
            </div>
        </div>

        <div class="applied">
            <div>
                <strong><?= $this->text('call-splash-applied_projects-header') ?></strong>
            </div>
            <div class="applied-data">
                <?= $this->matcher->getTotalProjects() ?>
            </div>
        </div>
        <div class="invest">
            <a class="btn btn-lg btn-block btn-lilac" href="/matcher/<?= $this->matcher->id ?>" tabindex="-1"><?= $this->text('landing-more-info') ?></a>
    </div>
    </div>
</div>
