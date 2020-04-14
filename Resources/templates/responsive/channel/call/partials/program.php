<?php

  $programs = $this->channel->getPrograms();
?>

<div class="section program">
  <div class="container">
    <h2 class="title"><span class="icon icon-calendar-2 icon-3x"></span>El programa Crowdcoop</h2>
    
    <div class="description">
      Enterate de todo lo que va a pasar.
    </div>
    <div class="accordion spacer-20 slider slider-programs">

    <?php foreach ($programs as $key => $program): ?>
      <div class="tabs <?= ($program->order == 1)? 'hover' : '' ?>">
        <div class="date"> <?= $program->date ?> </div>
        <div class="paragraph">
          <img class="img-responsive" src="/assets/img/channel/call/fase1.png" >
          <h1><?= $program->title ?></h1>
          <h3><?= $program->subtitle ?></h3>
          <p><?= $program->description ?></p>
          <a href="<?= $program->action_url ?>" class="btn btn-transparent"><i class="icon icon-projects icon-2x"></i> <?= $program->action ?></a>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>