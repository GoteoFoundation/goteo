<?php

$percent = $this->project->getAmountPercent();

?><div class="project-widget normal" id="project-<?= $this->project->id ?>">
    <a class="img-link" href="/project/<?= $this->project->id ?>">
        <img class="img-project" src="<?= $this->project->image->getLink(300, 208, true); ?>">
        <h2><?= $this->text_truncate($this->project->name, 80); ?></h2>
    </a>
    <div class="content">
        <h4>
            <a href="/user/profile/<?= $this->project->user->id?>" target="_blank"><?= $this->text('regular-by').' '.$this->project->user->name ?></a>
        </h4>
        <div class="description">
            <?= $this->text_truncate($this->project->description, 140) ?>
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
