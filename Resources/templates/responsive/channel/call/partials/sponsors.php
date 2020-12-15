<?php $channel=$this->channel; ?>

<?php if($channel->getSponsors()): ?>

<?php  
$main_sponsors=[];
$secondary_sponsors=[];
foreach ($channel->getSponsors() as $sponsor):
        if(!$sponsor->label)
          $main_sponsors[]=$sponsor;
        else
          $secondary_sponsors[]=$sponsor;
endforeach; 

?>

<div class="section sponsors">
  <div class="container">
    <h2 class="title"><span class="icon icon-rocket icon-3x"></span><?= $this->text('channel-call-sponsors-title') ?></h2>
    <p class="description"><?= $this->text('channel-call-sponsors-subtitle') ?></p>
    <ul class="img-list list-inline text-center">
    
   <ul class="list-inline text-left main-sponsors">
      <?php foreach ($main_sponsors as $sponsor): ?>
        <?php $sponsor_image=$sponsor->getImage(); ?>
        <li>
          <img src="<?= $sponsor_image->getLink(150, 80, false) ?>" >
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="row">

        <?php if(count($secondary_sponsors)==1): ?>
          <?php $sponsor=$secondary_sponsors[0]; ?>
          <?php $sponsor_image=$sponsor->getImage(); ?>
          <div class="col-md-9 col-sm-9 col-xs-9 secondary-sponsor">
            <div class="sponsor-label">
              <?= $sponsor->label ?>
            </div>
            <img src="<?= $sponsor_image->getLink(800, 0, false) ?>" >
          </div>

        <?php else: ?>

          <?php foreach($secondary_sponsors as $sponsor): ?>
          <?php $sponsor_image=$sponsor->getImage(); ?>
          <div class="col-md-4 col-sm-4 col-xs-6 secondary-sponsor">
            <div class="sponsor-label">
              <?= $sponsor->label ?>
            </div>
            <img src="<?= $sponsor_image->getLink(200, 65, false) ?>" >
          </div>
          <?php endforeach; ?>

        <?php endif; ?>

    </div>
  </div>

</div>

<?php endif; ?>