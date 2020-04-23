<?php

  $programs = $this->channel->getPrograms();
  $initial_slide = 0;

  foreach ($programs as $key => $program) {
    if ($program->date >= date('Y-m-d')) {
      $initial_slide = $key;
      continue;
    }
  }
?>

<div class="section program">
  <div class="container">
    <h2 class="title"><span class="icon icon-calendar-2 icon-3x"></span>El programa Crowdcoop</h2>
    
    <div class="description">
      Enterate de todo lo que va a pasar.
    </div>
    <div class="accordion spacer-20 slider slider-programs" data-initial-slide="<?= $initial_slide ?>">

    <?php foreach ($programs as $key => $program): ?>
      <div class="tabs">
        <div class="date"> <?= $program->date ?> </div>
        <div class="paragraph">
          <img class="img-responsive" src="<?= $program->getIcon()->getLink(350,150) ?>" >
          <h1><?= $program->title ?></h1>
          <p><?= $program->description ?></p>
          <a href="<?= $program->action_url ?>" class="btn btn-transparent"><i class="icon icon-projects icon-2x"></i> <?= $program->action ?></a>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>