<?php

  $programs = $this->channel->getPrograms();
?>

<div class="section program">
  <div class="container">
    <div class="title">
      <h1><i class="fa fa-calendar"></i> El programa Crowdcoop </h1>
    </div>
    
    <div class="description">
      Enterate de todo lo que va a pasar.
    </div>
    <ul class="accordion spacer-20">

    <?php foreach ($programs as $key => $program): ?>
      <li class="tabs <?= ($program->order == 1)? 'hover' : '' ?>">
        <div class="date"> <?= $program->date ?> </div>
        <div class="paragraph">
          <img class="img-responsive" src="/assets/img/channel/call/fase1.png" >
          <h1><?= $program->title ?></h1>
          <h3><?= $program->subtitle ?></h3>
          <p><?= $program->description ?></p>
          <a href="<?= $program->action_url ?>" class="btn btn-transparent"><i class="icon icon-projects icon-2x"></i> <?= $program->action ?></a>
        </div>
      </li>
      <!-- <li class="tabs">
        <div class="date"> 17/01/2020 </div>
        <div class="paragraph">
          <img class="img-responsive" src="/assets/img/channel/call/fase1.png" >
          <h1>FASE 01</h1>
          <h3>Recepcion de proyectos</h3>
          <p>Comienza el plazo de recepcion de proyectos para la convocatoria</p>
          <a href="/project/create" class="btn btn-transparent"><i class="icon icon-projects icon-2x"></i> Leer las bases</a>
        </div>
      </li>
      <li class="tabs">
        <div class="date"> 17/01/2020 </div>
        <div class="paragraph">
          <img class="img-responsive" src="/assets/img/channel/call/fase1.png" >
          <h1>FASE 01</h1>
          <h3>Recepcion de proyectos</h3>
          <p>Comienza el plazo de recepcion de proyectos para la convocatoria</p>
          <a href="/project/create" class="btn btn-transparent"><i class="icon icon-projects icon-2x"></i> Leer las bases</a>
        </div>
      </li>
      <li class="tabs">
        <div class="date"> 17/01/2020 </div>
        <div class="paragraph">
          <img class="img-responsive" src="/assets/img/channel/call/fase1.png" >
          <h1>FASE 01</h1>
          <h3>Recepcion de proyectos</h3>
          <p>Comienza el plazo de recepcion de proyectos para la convocatoria</p>
          <a href="/project/create" class="btn btn-transparent"><i class="icon icon-projects icon-2x"></i> Leer las bases</a>
        </div>
      </li>
      <li class="tabs">
        <div class="date"> 17/01/2020 </div>
        <div class="paragraph">
          <img class="img-responsive" src="/assets/img/channel/call/fase1.png" >
          <h1>FASE 01</h1>
          <h3>Recepcion de proyectos</h3>
          <p>Comienza el plazo de recepcion de proyectos para la convocatoria</p>
          <a href="/project/create" class="btn btn-transparent"><i class="icon icon-projects icon-2x"></i> Leer las bases</a>
        </div>
      </li>
      <li class="tabs">
        <div class="date"> 17/01/2020 </div>
        <div class="paragraph">
          <img class="img-responsive" src="/assets/img/channel/call/fase1.png" >
          <h1>FASE 01</h1>
          <h3>Recepcion de proyectos</h3>
          <p>Comienza el plazo de recepcion de proyectos para la convocatoria</p>
          <a href="/project/create" class="btn btn-transparent"><i class="icon icon-projects icon-2x"></i> Leer las bases</a>
        </div>
      </li>
      <li class="tabs">
        <div class="date"> 17/01/2020 </div>
        <div class="paragraph">
          <img class="img-responsive" src="/assets/img/channel/call/fase1.png" >
          <h1>FASE 01</h1>
          <h3>Recepcion de proyectos</h3>
          <p>Comienza el plazo de recepcion de proyectos para la convocatoria</p>
          <a href="/project/create" class="btn btn-transparent"><i class="icon icon-projects icon-2x"></i> Leer las bases</a>
        </div>
      </li> -->
    <?php endforeach; ?>
    </ul>
  </div>
</div>