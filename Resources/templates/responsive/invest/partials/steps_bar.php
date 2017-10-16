<div class="row bs-wizard hidden-xs steps-4" style="border-bottom:0;">

    <?php for($i=1; $i<5; $i++):
      if($i < $this->step)
        $class = 'complete';
      elseif ($i==$this->step)
        $class = 'active';
      else
        $class = 'disabled';
    ?>

     <div class="bs-wizard-step <?= $class ?>">
       <div class="text-center bs-wizard-stepnum"><?= $this->text('invest-step-' . $i) ?></div>
       <div class="progress"><div class="progress-bar"></div></div>
       <a href="#" class="bs-wizard-dot"></a>
    </div>

    <?php endfor; ?>

</div>
