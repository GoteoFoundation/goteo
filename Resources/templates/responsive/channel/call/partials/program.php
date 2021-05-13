<?php

  $programs = $this->channel->getPrograms();
  $initial_slide = 0;

  foreach ($programs as $key => $program) {
    if ($program->date >= date('Y-m-d')) {
      $initial_slide = $key;
      break;
    }
  }

  if ($programs) :
?>

<div class="section program">
  <div class="container">
    <h2 class="title">
        <span class="icon icon-calendar-2 icon-3x" style="<?= $this->colors['secondary'] ? "background-color:".$this->colors['secondary'] : '' ?>"></span>
        <span style="<?= $this->colors['primary'] ? "color:".$this->colors['primary'] : '' ?>" >
        <?= $this->t('channel-call-program-title').$this->channel->name ?>
        </span>
        <a class="btn btn-transparent" href="<?= $this->channel->terms_url ?>" class="btn btn-transparent" style="<?= $this->colors['primary'] ? "color:".$this->colors['primary'] : '' ?>">
          <i class="icon icon-terms icon-2x" style="<?= $this->colors['secondary'] ? "background-color:".$this->colors['secondary'] : '' ?>"></i> <?= $this->t('channel-call-program-terms') ?>
        </a>
    </h2>
    <div class="description" style="<?= $this->colors['secondary'] ? "color:".$this->colors['secondary'] : '' ?>">
      <?= $this->t('channel-call-program-description') ?>
    </div>
    <div class="accordion spacer-20 slider slider-programs" data-initial-slide="<?= $initial_slide ?>">

    <?php foreach ($programs as $key => $program): ?>
      <div class="tabs">
        <div class="date" style="<?= $this->colors['primary'] ? "color:".$this->colors['primary'].";" : '' ?> <?= $this->colors['secondary'] ? "
border-left-color:".$this->colors['secondary'] : '' ?>" > 
          <?= date('d/m/Y', strtotime($program->date)) ?>
        </div>
        <div class="paragraph">
          <?php if($program->header): ?>
            <img loading="lazy" class="img-responsive" src="<?= $program->getHeader()->getLink(0,120) ?>" >
          <?php endif; ?>
          <h1 style="<?= $this->colors['primary'] ? "color:".$this->colors['primary'] : '' ?>" ><?= $program->title ?></h1>
          <p style="<?= $this->colors['secondary'] ? "color:".$this->colors['secondary'] : '' ?>"><?= $program->description ?></p>
          <?php if ($program->action_url): ?>
            <a href="<?= $program->action_url ?>" class="btn btn-transparent"><i class="icon icon-<?= $program->icon ?> icon-2x"></i> <?= $program->action ?></a>
          <?php elseif ($program->modal_description): ?>
           <a href="#" data-toggle="modal" data-target="#programModal-<?=$program->id ?>" class="btn btn-transparent" style="<?= $this->colors['primary'] ? "color:".$this->colors['primary'] : '' ?>"><i class="icon icon-<?= $program->icon ?> icon-2x" style="<?= $this->colors['secondary'] ? "color:".$this->colors['secondary'] : '' ?>"></i> <?= $program->action ?></a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>

<?php endif; ?>